# Test Coverage Plan

**Total coverage at start:** 61.9%

## Status

| # | File | Priority | Status |
|---|------|----------|--------|
| 1 | `tests/Feature/Models/ReportModelTest.php` | High | ✅ Done (10 tests) |
| 2 | `tests/Feature/Policies/PostPolicyTest.php` | High | ✅ Done (14 tests) |
| 3 | `tests/Feature/Policies/SectionPolicyTest.php` | High | ✅ Done (12 tests) |
| 4 | `tests/Feature/PostControllerTest.php` | High | ✅ Done (14 tests) |
| 5 | `tests/Feature/RestoreControllerTest.php` | Medium | ✅ Done (8 tests) |
| 6 | `tests/Feature/SearchControllerTest.php` | Medium | ✅ Done (9 tests) |

**Coverage: 61.9% → 68.1% (+6.2pp). Tests: 267 → 334 (+67 tests).**

---

## Round 2 — Improvable gaps (68.1% baseline)

| # | File | Priority | Status |
|---|------|----------|--------|
| 7 | `tests/Feature/Policies/TopicPolicyTest.php` | High | ✅ Done (15 tests) |
| 8 | `tests/Feature/Policies/UserPolicyTest.php` | Medium | ✅ Done (3 tests) |
| 9 | `tests/Unit/View/Components/UserCardTest.php` | Low | ✅ Done (10 tests) |
| 10 | `tests/Feature/Models/ReportModelTest.php` (extend) | Medium | ✅ Done (+2 tests) |
| 11 | `tests/Feature/Livewire/SettingsTest.php` | Medium | ✅ Done (5 tests) |
| 12 | `tests/Feature/Livewire/PostComposeTest.php` | Medium | ✅ Done (2 tests) |

**Coverage: 68.1% → 69.8% (+1.7pp). Tests: 334 → 372 (+38 tests).**

**Note:** `TopicPolicy::create` dispatches to TopicPolicy (not SectionPolicy) via `$user->can('create', [Topic::class, $section])` — passing the model class string as the first array element routes to the correct policy when the additional argument is a different model type.

### 7. `TopicPolicyTest` — all methods including `before()` admin bypass

- `admin_bypasses_all_checks` — before() returns true for admin
- `viewDetails_returns_true_for_non_secret_topic`
- `viewDetails_returns_true_for_moderator_of_secret_topic`
- `viewDetails_returns_false_for_non_moderator_of_secret_topic`
- `create_allows_section_moderator`
- `create_allows_user_when_section_permits_user_topics`
- `create_denies_regular_user_when_section_disallows_topics`
- `update_allows_section_moderator`
- `update_denies_non_moderator`
- `move_allows_moderator_of_both_sections`
- `move_denies_when_not_moderating_destination`
- `delete_allows_section_moderator`
- `delete_denies_non_moderator`
- `reply_allows_moderator_on_readonly_topic`
- `reply_allows_regular_user_on_open_topic`
- `reply_denies_regular_user_on_readonly_topic`

### 8. `UserPolicyTest` — update authorization

- `user_can_update_their_own_profile`
- `user_cannot_update_another_users_profile`
- `guest_cannot_update_any_profile`

### 9. `UserCardTest` — pure unit tests for score-based CSS helpers

- `classy_returns_text_secondary_for_very_low_score`
- `classy_returns_text_dark_for_low_score`
- `classy_returns_text_info_for_medium_score`
- `classy_returns_text_primary_for_high_score`
- `classy_returns_text_success_for_very_high_score`
- `classy_returns_text_danger_for_max_score`
- `heading_background_is_light_for_zero_score`
- `heading_background_is_info_for_nonzero_score`
- `heading_foreground_is_secondary_for_zero_score`
- `heading_foreground_is_white_for_nonzero_score`

### 10. `ReportModelTest` (extend) — reportable_link accessor

- `reportable_link_returns_url_for_post_reportable`
- `reportable_link_returns_null_for_non_post_morph`

### 11. `SettingsTest` — Livewire Settings component

- `admin_can_render_settings_component`
- `admin_can_save_mode_settings`
- `admin_can_set_bbs_mode`
- `non_admin_cannot_save_settings`
- `non_admin_cannot_set_bbs_mode`

### 12. `PostComposeTest` — Livewire PostCompose mount

- `mount_without_reply_leaves_text_empty`
- `mount_with_reply_prefills_text_with_quoted_content`

---

## 1. `ReportModelTest` — scopes and accessors on `Models/Report`

**Tests:**
- `test_open_scope_excludes_closed_reports`
- `test_closed_scope_returns_only_closed_reports`
- `test_status_badge_class_for_new`
- `test_status_badge_class_for_under_review`
- `test_status_badge_class_for_closed`
- `test_status_label_returns_human_readable_label`
- `test_reason_label_returns_human_readable_label`
- `test_snapshot_text_returns_text_from_json`
- `test_snapshot_text_returns_fallback_when_text_missing`

**Notes:** Report factory definition is empty — all attributes must be passed explicitly.

---

## 2. `PostPolicyTest` — create / update / delete authorization

**Setup:** home section → sub-section (moderator) → topic → post (author). Also: admin, unrelated user.

**`create` tests:**
- `test_guest_cannot_create_post`
- `test_admin_can_always_create_post`
- `test_moderator_can_create_in_readonly_topic`
- `test_regular_user_can_create_in_open_topic`
- `test_regular_user_cannot_create_in_readonly_topic`

**`update` tests:**
- `test_admin_can_update_any_post`
- `test_moderator_can_update_post_in_their_section`
- `test_author_can_update_recent_most_recent_post`
- `test_author_cannot_update_expired_post`
- `test_author_cannot_update_non_most_recent_post`
- `test_unrelated_user_cannot_update_post`

**`delete` tests:**
- `test_admin_can_delete_any_post`
- `test_moderator_can_delete_post_in_their_section`
- `test_regular_user_cannot_delete_post`

**Notes:** Time-sensitive update tests need `Config::set('nexus.recent_edit', 300)`.

---

## 3. `SectionPolicyTest` — create / update / move / delete / restore

**Setup:** home (sysop) → section (moderator) → childSection. Also: destination section, unrelated user.

**Tests:**
- `test_moderator_can_create_subsection`
- `test_non_moderator_cannot_create_subsection`
- `test_moderator_can_update_own_section`
- `test_parent_moderator_can_update_child_section`
- `test_unrelated_user_cannot_update_section`
- `test_parent_moderator_can_move_section_to_moderated_destination`
- `test_user_without_destination_moderation_cannot_move_section`
- `test_parent_moderator_can_delete_child_section`
- `test_non_parent_moderator_cannot_delete_section`
- `test_user_moderating_both_can_restore_section`
- `test_user_moderating_only_destination_cannot_restore`

---

## 4. `PostControllerTest` — update / destroy / report

**Setup:** Same chain as PolicyTest above.

**`update` tests:**
- `test_admin_can_update_post`
- `test_moderator_can_update_post`
- `test_author_can_update_recent_post`
- `test_unrelated_user_cannot_update_post` (403)
- `test_update_requires_text`
- `test_update_redirects_back`

**`destroy` tests:**
- `test_admin_can_delete_post`
- `test_moderator_can_delete_post`
- `test_unrelated_user_cannot_delete_post` (403)
- `test_destroy_redirects_to_topic`

**`report` tests:**
- `test_authenticated_user_can_view_report_page`
- `test_report_shows_anonymous_for_secret_topic`
- `test_report_not_anonymous_for_regular_topic`
- `test_unauthenticated_user_cannot_view_report_page`

**Notes:** `UpdatePost` payload shape: `['id' => $post->id, 'form' => [$post->id => ['text' => '...', 'title' => '...']]]`. `destroy` uses `forceDelete` — use `assertDatabaseMissing`.

---

## 5. `RestoreControllerTest` — index / section / topic

**Setup:** Full chain with soft-deleted section and topic.

**Tests:**
- `test_user_can_view_restore_index`
- `test_unauthenticated_user_cannot_view_restore_index`
- `test_moderator_can_restore_trashed_section`
- `test_cannot_restore_section_without_permission` (403)
- `test_restoring_section_moves_it_to_destination`
- `test_moderator_can_restore_trashed_topic`
- `test_cannot_restore_topic_without_permission` (403)
- `test_restoring_topic_moves_it_to_destination`

**Notes:** Routes use integer ID params, not model binding. `$request->destination` is a request body param. Soft-delete records in `setUp()` before tests act on them.

---

## 6. `SearchControllerTest` — index / submitSearch / find

**Setup:** Needs home section for breadcrumbs. Posts with known text for search results.

**Tests:**
- `test_authenticated_user_can_view_search_page`
- `test_unauthenticated_user_is_redirected`
- `test_submit_search_redirects_to_find`
- `test_submit_search_requires_text`
- `test_find_returns_results_for_matching_word`
- `test_find_returns_view_when_no_results`
- `test_find_with_multiple_words`

---

## Cross-cutting notes

- All controller tests need a home section (`parent_id = null`) for breadcrumbs
- `User::factory()->create()` requires a `Theme` to exist — check factory or pre-create one
- `Post` factory: `for($topic)` + `for($user, 'author')`
- `Topic` factory: `for($section, 'section')`
- `Section` factory: `for($moderator, 'moderator')` + `for($parent, 'parent')`
