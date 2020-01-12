CREATE TABLE IF NOT EXISTS `users` (
 `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
 `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `email_verified_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `accessed_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
 `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `user_id` bigint(20) UNSIGNED NOT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `posts_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
 `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `post_id` bigint(20) UNSIGNED NOT NULL,
 `user_id` bigint(20) UNSIGNED NOT NULL,
 `created_at` timestamp NOT NULL,
 `approved_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `comments_post_id_foreign` (`post_id`),
 KEY `comments_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `posts` ADD CONSTRAINT `posts_user_id_foreign` 
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`)
    REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE;