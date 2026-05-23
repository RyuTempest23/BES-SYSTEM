---
name: create-skill
description: 'Create a reusable skill file (SKILL.md) that captures a workflow, decision points, and quality checks.'
argument-hint: What should this skill produce?
---

Guide the user to create a `SKILL.md`.

## What to do

1. Review the conversation and any available repository context.
2. Identify the workflow or process being followed.
3. Extract the step-by-step process, decision points, and completion criteria.
4. Determine scope: workspace-scoped or personal.
5. Draft the skill with the proper frontmatter and clear guidance.
6. Validate the skill after creation.

## If the workflow is unclear

- Ask the user what outcome the skill should produce.
- Ask whether the skill should be workspace-scoped or personal.
- Ask whether they want a quick checklist or a full multi-step workflow.

## Validation checklist

- Does the skill have valid YAML frontmatter?
- Is `name` present and meaningful?
- Is `description` clear and discoverable?
- Does the skill body explain the task clearly?
- Is the file saved in an appropriate location for workspace customizations?

## Suggested follow-up customizations

- Create a `create-prompt` prompt for single-step task creation.
- Add a workspace `copilot-instructions.md` entry describing when to use this skill.
- Create a `create-agent` custom agent if the workflow needs deterministic tool restrictions.
