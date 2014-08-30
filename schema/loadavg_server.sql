--
-- LoadAvg Server Database schema
--

-- create database `loadavg`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `server_count` int(11) NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  UNIQUE KEY `api_token` (`api_token`, `email`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12;


INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `api_token`, `server_count`, `created_at`, `updated_at`) VALUES
(1, 'Alex', 'Cross', 'user1@example.com', 'loadavg_53101ad10205f', '1423138b823c278d7f69a3f899da75ad', '8FE2D3C599794CD531D73896CD59E52A', 0, NOW(), NOW()),
(2, 'John', 'Doe', 'user2@example.com', 'loadavg_5313bd3e9a0d1', '0b617d693ef9d31347456c9a2ae493f7', 'BEA8D60F1A8031C0547AA7214E394895', 0, NOW(), NOW()),
(3, 'Mary', 'Jane', 'user3@example.com', 'loadavg_5313bd4085c29', 'dfd1452a5d9c71319d1bb3e3f9491420', '618E3B461ADC9771AB38E38013FE33E0', 0, NOW(), NOW()),
(4, 'Timmy', 'Turner', 'user4@example.com', 'loadavg_5313bd4267907', '30b897c191a8d0d0792dabcce7b00024', '101DE659B0A614A1925516284A047A7D', 0, NOW(), NOW()),
(5, 'Jens', 'Jargon', 'user5@example.com', 'loadavg_5313bd4549d8c', 'da65be8d279de1f820b5bd8a3331ec76', '90D56E2F283850CFFF6AE14E95C6C05E', 0, NOW(), NOW()),
(6, 'Petr', 'Schmit', 'user6@example.com', 'loadavg_5313bd5032081', '8a06e65d76aad87a4a2a14fceb59d7dd', '679CA70360A840B1DE64A57832EAD203', 0, NOW(), NOW()),
(7, 'Dida', 'King', 'user7@example.com', 'loadavg_531404a943038', '0488838ddbad9e09cda3cd3b3a02de03', '5427F4F524084A66C1FB15193B1688F9', 0, NOW(), NOW()),
(8, 'Stacey', 'Whitticker', 'user8@example.com', 'loadavg_53221e5802b27', '6565384554d582187c4330c7b01b8931', '396CBA85B13B1266BA500B96A39ADF97', 0, NOW(), NOW()),
(9, 'Audrey', 'Price', 'user9@example.com', 'loadavg_5331fae27263a', '2266faaef8e550cea86fed647b97b126', '0DDAEF84E9A382EC979C97722B31F83E', 0, NOW(), NOW()),
(10, 'Jonnathan', 'Jones', 'user10@example.com', 'loadavg_5331fb773c11b', 'b4284466fcc36f749ae7d16d0e2e7f5b', '5A2ABC1E08AEF90B409ED49F3C890794', 0, NOW(), NOW()),
(11, 'Heidi', 'Klum', 'user11@example.com', 'loadavg_53331b6fecc53', '7709c813dc79bbe68df1e90a2df1abc9', '8B61B9D5A54D990E5E028D42A9BE5D6D', 0, NOW(), NOW());



CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `server_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `server_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`)
  REFERENCES users (`id`)
  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



CREATE TABLE `server_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  FOREIGN KEY (`server_id`)
  REFERENCES servers (`id`)
  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16;

-- INSERT INTO `server_data` (`id`, `server_id`, `data`, `created_at`, `updated_at`) VALUES
-- (1, 5, '{"Hardware":{"data":"1393572905|0.04|0.04|0.05\n","timestamp":"1393572905"},"Memory":{"data":"1393572905|1857136|1855708\n","timestamp":"1393572905"},"Network":{"data":"1393572905|0.0|0.0\n","timestamp":"1393572905"}}', NOW(), NOW()),
-- (2, 5, '{"Hardware":{"data":"1393573203|0.09|0.06|0.05\n","timestamp":"1393573203"},"Memory":{"data":"1393573203|1856396|1856372\n","timestamp":"1393573203"},"Network":{"data":"1393573203|0.0|0.0\n","timestamp":"1393573203"}}', NOW(), NOW()),
-- (3, 5, '{"Hardware":{"data":"1393573501|0.13|0.09|0.06\n","timestamp":"1393573501"},"Memory":{"data":"1393573501|1865332|1867060\n","timestamp":"1393573501"},"Network":{"data":"1393573501|0.08|0.17\n","timestamp":"1393573501"}}', NOW(), NOW()),
-- (4, 5, '{"Hardware":{"data":"1393575356|0.39|0.17|0.09\n","timestamp":"1393575356"},"Memory":{"data":"1393575356|1867548|1867780\n","timestamp":"1393575356"},"Network":{"data":"1393575356|0.0|0.0\n","timestamp":"1393575356"}}', NOW(), NOW()),
-- (5, 6, '{"Hardware":{"data":"1393575601|0.07|0.15|0.11\n","timestamp":"1393575601"},"Memory":{"data":"1393575601|1870852|1872132\n","timestamp":"1393575601"},"Network":{"data":"1393575601|0.1|0.15\n","timestamp":"1393575601"}}', NOW(), NOW()),
-- (6, 6, '{"Hardware":{"data":"1393575901|0.03|0.12|0.12\n","timestamp":"1393575901"},"Memory":{"data":"1393575902|1864672|1864628\n","timestamp":"1393575902"},"Network":{"data":"1393575902|0|0\n","timestamp":"1393575902"}}', NOW(), NOW()),
-- (7, 6, '{"Hardware":{"data":"1393576201|0.06|0.09|0.11\n","timestamp":"1393576201"},"Memory":{"data":"1393576201|1869872|1869880\n","timestamp":"1393576201"},"Network":{"data":"1393576201|0.06|0.05\n","timestamp":"1393576201"}}', NOW(), NOW()),
-- (8, 7, '{"Hardware":{"data":"1393576505|0.20|0.14|0.13\n","timestamp":"1393576505"},"Memory":{"data":"1393576505|1872340|1872508\n","timestamp":"1393576505"},"Network":{"data":"1393576505|0.07|0.07\n","timestamp":"1393576505"}}', NOW(), NOW()),
-- (9, 7, '{"Hardware":{"data":"1393576804|0.18|0.13|0.13\n","timestamp":"1393576804"},"Memory":{"data":"1393576804|1873800|1874000\n","timestamp":"1393576804"},"Network":{"data":"1393576804|0.0|0.0\n","timestamp":"1393576804"}}', NOW(), NOW()),
-- (10, 7, '{"Hardware":{"data":"1393578901|0.02|0.09|0.12\n","timestamp":"1393578901"},"Memory":{"data":"1393578901|1873360|1873764\n","timestamp":"1393578901"},"Network":{"data":"1393578901|0.01|0.02\n","timestamp":"1393578901"}}', NOW(), NOW()),
-- (11, 8, '{"Hardware":{"data":"1393579205|0.09|0.08|0.12\n","timestamp":"1393579205"},"Memory":{"data":"1393579205|1870588|1869944\n","timestamp":"1393579205"},"Network":{"data":"1393579205|0.0|0.0\n","timestamp":"1393579205"}}', NOW(), NOW()),
-- (12, 8, '{"Hardware":{"data":"1393579504|0.16|0.08|0.11\n","timestamp":"1393579504"},"Memory":{"data":"1393579504|1869828|1869488\n","timestamp":"1393579504"},"Network":{"data":"1393579504|0.0|0.0\n","timestamp":"1393579504"}}', NOW(), NOW()),
-- (13, 8, '{"Hardware":{"data":"1393579801|0.02|0.06|0.10\n","timestamp":"1393579801"},"Memory":{"data":"1393579801|1871000|1869780\n","timestamp":"1393579801"},"Network":{"data":"1393579801|0.0|0.0\n","timestamp":"1393579801"}}', NOW(), NOW()),
-- (14, 8, '{"Hardware":{"data":"1393580105|0.00|0.02|0.07\n","timestamp":"1393580105"},"Memory":{"data":"1393580105|1870440|1870472\n","timestamp":"1393580105"},"Network":{"data":"1393580105|0.0|0.0\n","timestamp":"1393580105"}}', NOW(), NOW()),
-- (15, 8, '{"Hardware":{"data":"1393580406|0.00|0.02|0.05\n","timestamp":"1393580406"},"Memory":{"data":"1393580406|1873956|1875520\n","timestamp":"1393580406"},"Network":{"data":"1393580406|0.07|0.06\n","timestamp":"1393580406"}}', NOW(), NOW());



-- CREATE TABLE `server_config` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `server_id` int(11) NOT NULL,
--   `server_data` text COLLATE utf8_unicode_ci NOT NULL,
--   `created_at` datetime NOT NULL,
--   `updated_at` datetime NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `server_id` (`server_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


