# Issue Tracker

A Linear/Jira-style issue tracker: issues are organized into projects and moved through a workflow on a board.

## Language

**User**:
A person who can access the system.
_Avoid_: Assignee (as the entity name — "assignee" is a role a User plays on an Issue, not the entity itself), Account.

**Auth Identity**:
A User's credential for signing in via a specific method (e.g. Google SSO, later email/password). A User can have multiple Auth Identities (1:n) — the User is the stable identity; sign-in methods are attachable/detachable credentials, not the User itself.
_Avoid_: Login, Credential (as the entity name), and conflating this with User.

**Project Membership**:
The many-to-many relationship between a `User` and a `Project`. Carries a project-scoped `role` (rights are granted per membership, not globally on the User) — e.g. a user can be Admin on one project and a plain Member on another. `role` is a fixed small set (Admin, Member) — no granular/configurable permissions.
_Avoid_: Access, Permission (as the entity name).

**Project**:
A container that issues belong to, identified by a short `key` (e.g. `COR`) used as the prefix for issue IDs.

**Issue**:
A unit of work tracked on the board, belonging to a Project, with a `status` (workflow stage), `priority`, a set of Labels, a single nullable assignee, and a reporter.

**Reporter**:
The User who created an Issue. Distinct from and independent of the assignee; always set (non-nullable) at creation time.

**Label**:
A first-class, reusable tag entity (id, name, and eventually color) applied to Issues, scoped per-project. Not a freeform string.
_Avoid_: Tag (as the entity name).

**Issue Identity**:
An Issue's id (e.g. `COR-140`) is the Project's `key` plus a number from a per-project sequence — each Project counts its own issues independently, which is why the key exists (to disambiguate across projects).

**Comment**:
A first-class entity attached to an Issue, authored by a User, with a body and timestamp.

**Status**:
The workflow stage of an Issue (`backlog`, `todo`, `in-progress`, `in-review`, `done`), rendered as a board column. Single global workflow shared by all Projects — not per-project configurable.
_Avoid_: Column, State (as the entity name — "column" is just the board's rendering of a Status).
