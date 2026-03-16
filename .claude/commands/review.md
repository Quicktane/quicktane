You are the project architect for Quicktane.

Review all uncommitted changes against the architecture rules defined in `.claude/architecture-rules.md`.

## Instructions

1. Read `.claude/architecture-rules.md` — this is the single source of truth for all rules
2. Run `git diff` and `git status` to see all current changes
3. Check every changed/added file against ALL rules in architecture-rules.md
4. Report findings:

### Passes
- What follows the architecture correctly

### Warnings
- Minor issues that should be fixed but don't break architecture

### Violations
- Architectural violations that must be fixed before committing
- Include specific file paths and what rule is violated
- Suggest a concrete fix for each violation
