---
status: accepted
---

# Soft-delete via `deletedAt` on all entities

We decided every entity (User, Project, ProjectMembership, Label, Issue, IssueLabel, Comment) carries `createdAt`, `updatedAt`, and `deletedAt` timestamps, with `deletedAt` used for soft-delete instead of removing rows outright. This preserves history/audit trail and avoids cascading hard-deletes across relations (e.g. a deleted Project's Issues), at the cost of every query needing to filter out soft-deleted rows and uniqueness constraints (e.g. Project `key`) needing to account for soft-deleted records.

**Project `key` is permanently retired on delete.** The uniqueness constraint on `Project.key` applies across all rows regardless of `deletedAt` (a plain column-level unique index, not a partial one), so a new Project can never reuse a deleted Project's `key`. Chosen over making the key reusable because a reused key would let `COR-140` mean a different project's issue than it used to for anyone who'd seen the old one — reusability was rejected in favor of keeping history unambiguous, which is the same reason soft-delete exists in the first place.

**Soft-delete cascades down ownership relations.** Deleting a Project soft-deletes its Issues, Labels, and ProjectMemberships; deleting an Issue soft-deletes its Comments and IssueLabels. None of these can meaningfully outlive their owning Project (or Issue) — e.g. an Issue's own identity (`{Project.key}-{number}`) depends on a live Project, and a ProjectMembership to a dead Project is meaningless — so leaving them active would be a dangling reference. Doctrine's `deletedAt` is just a timestamp column; it doesn't cascade automatically, so this must be enforced explicitly wherever a Project/Issue delete is performed (application code / a domain service), not left to the ORM.

**Deleting a User keeps the row (soft-deleted), rather than cascading, because Issue.reporter and Comment.author are required (non-nullable) references.** The User row must keep existing so those required foreign keys stay valid — a reported Issue or authored Comment still shows the (deleted) User rather than losing referential integrity or rewriting history. The one exception is `Issue.assignee`, which is nullable: on User delete, that link is cleared so the Issue reverts to unassigned instead of pointing at a ghost user.

**Soft-deleted data is kept indefinitely, not purged.** No compliance or storage-cost driver has come up to justify a retention/purge policy yet; adding a purge job later is cheap to layer on top of this model, whereas committing to a retention period now would be an unfounded guess.
