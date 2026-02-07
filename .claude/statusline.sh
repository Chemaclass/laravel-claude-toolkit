#!/bin/bash
# Shared StatusLine for Claude Code
# Shows: robbyrussell prompt + model + context %
# Override locally via .claude/settings.local.json

input=$(cat)

# Parse values in single jq call
eval $(echo "$input" | jq -r '
  @sh "dir=\(.workspace.current_dir // .cwd // "unknown")",
  @sh "context_pct=\(.context_window.used_percentage // 0)",
  @sh "model=\(.model.display_name // "Claude")"
')

# Robbyrussell-style prompt
printf '\033[1;32m➜\033[0m  \033[36m%s\033[0m' "$(basename "$dir")"

# Git info
if git -C "$dir" --no-optional-locks rev-parse --git-dir > /dev/null 2>&1; then
  branch=$(git -C "$dir" --no-optional-locks branch --show-current 2>/dev/null)
  [ -z "$branch" ] && branch=$(git -C "$dir" --no-optional-locks rev-parse --short HEAD 2>/dev/null)
  if [ -n "$branch" ]; then
    printf ' \033[1;34mgit:(\033[31m%s\033[34m)\033[0m' "$branch"
    if ! git -C "$dir" --no-optional-locks diff --quiet 2>/dev/null || \
       ! git -C "$dir" --no-optional-locks diff --cached --quiet 2>/dev/null; then
      printf ' \033[33m✗\033[0m'
    fi
  fi
fi

# Model name (dimmed)
printf '  \033[90m%s\033[0m' "$model"

# Context % - color based on usage (cyan < 70%, yellow 70-85%, red > 85%)
context_int=${context_pct%.*}
if [ "$context_int" -gt 85 ]; then
  printf '  \033[31m%s%%\033[0m' "$context_pct"
elif [ "$context_int" -gt 70 ]; then
  printf '  \033[33m%s%%\033[0m' "$context_pct"
else
  printf '  \033[36m%s%%\033[0m' "$context_pct"
fi
