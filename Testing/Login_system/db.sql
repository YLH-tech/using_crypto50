-- CREATE DATABASE
-- CREATE DATABASE `project`;




-- for gorgot password

CREATE TABLE `codes` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(5) NOT NULL,
  `expire` int(11) NOT NULL
);

ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `expire` (`expire`),
  ADD KEY `email` (`email`);

  ALTER TABLE `codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


  -- for users

  CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `verification_code` TEXT NOT NULL,
  `email_verified_at` datetime NULL
);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  