---
status: proposed
---

# Soft-delete via `deletedAt` on all entities

We decided every entity (User, Project, ProjectMembership, Label, Issue, IssueLabel, Comment) carries `createdAt`, `updatedAt`, and `deletedAt` timestamps, with `deletedAt` used for soft-delete instead of removing rows outright. This preserves history/audit trail and avoids cascading hard-deletes across relations (e.g. a deleted Project's Issues), at the cost of every query needing to filter out soft-deleted rows and uniqueness constraints (e.g. Project `key`) needing to account for soft-deleted records.

**Needs future investigation**: how soft-delete interacts with uniqueness constraints (can a new Project reuse a deleted Project's `key`?), cascading soft-delete behavior (does deleting a Project soft-delete its Issues?), and whether soft-deleted data is ever purged.
