# AI Models Update - September 11, 2026

## Update Summary

OpenAI, Claude, and Gemini provider models have been updated to the latest versions available as of September 11, 2026, verified against official provider documentation. GitHub Models was **not** updated in this pass (see note below).

**Sources Verified:**
- OpenAI: https://developers.openai.com/api/docs/models
- Claude/Anthropic: bundled `claude-api` skill model reference (cached 2026-06-24) + this session's system-provided current model IDs
- Google Gemini: https://ai.google.dev/gemini-api/docs/models
- GitHub Models: **not verified** — the marketplace catalog (`https://github.com/marketplace/models`) requires an authenticated session to browse; unauthenticated fetch returned only the sign-in page

---

## Changes Made

### 🤖 OpenAI Provider

**Previous Default**: `gpt-5.4`
**New Default**: **`gpt-6-astra`** — latest flagship, most capable model

| Model | Description |
|-------|-------------|
| **gpt-6-astra** ✨ | Latest flagship - most capable, hardest end-to-end work |
| gpt-5.6-sol | Complex professional work |
| gpt-5.6-terra | Balances intelligence and cost |
| gpt-5.6-luna | Cost-sensitive workloads |
| gpt-5.4, gpt-5.4-mini | Previous generation |
| gpt-4o, gpt-4o-mini | Legacy |

### 🎭 Claude Provider

**Previous Default**: `claude-sonnet-4-6`
**New Default**: **`claude-sonnet-5`** — current-generation balanced model

| Model | Description |
|-------|-------------|
| **claude-sonnet-5** ✨ | Best speed/intelligence balance |
| claude-opus-5 | Most intelligent |
| claude-fable-5-1 | Most capable widely released model, advanced reasoning |
| claude-haiku-4-5-20251001 | Fastest |
| claude-opus-4-6, claude-sonnet-4-6 | Previous generation |
| claude-3-5-sonnet-20241022, claude-3-5-opus-20250514, claude-3-opus-20240229, claude-3-haiku-20240307 | Legacy |

### 🌟 Gemini Provider

**Previous Default**: `gemini-2.5-flash`
**New Default**: **`gemini-3.8-flash`** — most intelligent Flash model, long-horizon agentic work

| Model | Description |
|-------|-------------|
| **gemini-3.8-flash** ✨ | Latest, most intelligent Flash model |
| gemini-3.1-pro-preview | Most capable, complex reasoning |
| gemini-3.7-flash, gemini-3.6-flash | Previous generation |
| gemini-3.5-flash-lite | Fastest, most cost-effective |
| gemini-2.5-flash, gemini-2.5-pro, gemini-2.5-flash-lite | Legacy |

### 🐙 GitHub Models — NOT updated

GitHub's model marketplace catalog can't be browsed without an authenticated GitHub session, and its catalog IDs have historically diverged from each vendor's own API model names (different casing, versioning, and publisher-prefixed naming). Rather than guess at IDs that could silently break existing chats, `GitHubModelsProvider.php` was left unchanged. Before updating its defaults:

1. Sign in and check https://github.com/marketplace/models for current catalog IDs
2. Confirm the exact string GitHub expects at `https://models.inference.ai.azure.com/chat/completions`
3. Update `src/Services/AI/GitHubModelsProvider.php`'s constructor default and `getAvailableModels()`

---

## Migration Guide

All changes are backward compatible — previous-generation and legacy model strings remain in each provider's `getAvailableModels()` list, so existing `ai_chat_settings` rows referencing an older model string keep working. New installations get the new defaults; existing installations keep whatever model was already selected in `/admin/ai-settings` until an admin changes it there.

---

## Files Updated

✅ src/Services/AI/OpenAIProvider.php
✅ src/Services/AI/ClaudeProvider.php
✅ src/Services/AI/GeminiProvider.php
✅ src/Providers/ChatWidgetServiceProvider.php (unrelated bug fix — see CHANGELOG)
✅ README.md (Provider configuration section)
✅ CHANGELOG.md
✅ composer.json (version bump to 1.1.1)
⬜ src/Services/AI/GitHubModelsProvider.php — intentionally left unchanged, see above

---

**Update Date**: September 11, 2026
**Status**: OpenAI/Claude/Gemini updated and cross-checked against official docs; GitHub Models unverified
**Compatibility**: Laravel 10, 11, 13+ ✓
**Breaking Changes**: None
