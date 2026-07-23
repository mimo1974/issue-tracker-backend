# Domain Model

Entities, properties, and relationships for the issue tracker. Companion to the vocabulary in [`CONTEXT.md`](../CONTEXT.md) and the decisions in [`docs/adr/`](./adr/). Intended as the source for generating the Doctrine entities on the backend.

All entities carry `createdAt`, `updatedAt`, `deletedAt` (soft-delete — see [ADR-0001](./adr/0001-soft-delete-via-deletedat.md)); omitted below for brevity.

## User

- `id`, `name`, `initials`
- 1:n → `AuthIdentity`
- 1:n → `ProjectMembership`
- 1:n → `Issue` as assignee (nullable on Issue's side — a User has 0..n assigned issues; an Issue has 0..1 assignee)
- 1:n → `Issue` as reporter (required on Issue's side)
- 1:n → `Comment` as author

## AuthIdentity

- `id`, `userId`, `provider` (`google` | `email`, extensible), `providerUserId`/`email`, `passwordHash` (nullable, only for `email` provider)
- n:1 → `User` (required)

## Project

- `id`, `name`, `key`
- n:n → `User` via `ProjectMembership`
- 1:n → `Issue`
- 1:n → `Label`

## ProjectMembership

*(join entity, User n:n Project)*

- `id`, `userId`, `projectId`, `role` (`admin` | `member`)
- n:1 → `User` (required)
- n:1 → `Project` (required)

## Label

- `id`, `projectId`, `name`
- n:1 → `Project` (required)
- n:n → `Issue` via `IssueLabel`

## Issue

- `id` (Project `key` + per-project sequence number), `title`, `status` (`backlog` | `todo` | `in_progress` | `in_review` | `done`), `priority` (`low` | `medium` | `high` | `urgent`), `dueDate` (nullable)
- n:1 → `Project` (required)
- n:1 → `User` as assignee (nullable)
- n:1 → `User` as reporter (required)
- n:n → `Label` via `IssueLabel`
- 1:n → `Comment`

## IssueLabel

*(join entity, Issue n:n Label)*

- `issueId`, `labelId`
- n:1 → `Issue` (required)
- n:1 → `Label` (required)

## Comment

- `id`, `issueId`, `authorId`, `body`
- n:1 → `Issue` (required)
- n:1 → `User` as author (required)
