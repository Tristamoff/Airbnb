CREATE TABLE `airbnb` (
  `letter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  UNIQUE KEY `x_y` (`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `airbnb` (`letter`, `x`, `y`) VALUES
('п',	1,	1),
('у',	1,	2),
('х',	1,	3),
('е',	1,	4),
('ы',	1,	5),
('в',	1,	6),
('ю',	1,	7),
('о',	1,	8),
('ф',	1,	9),
('е',	2,	1),
('ь',	2,	2),
('п',	2,	3),
('щ',	2,	4),
('ф',	2,	5),
('м',	2,	6),
('и',	2,	7),
('в',	2,	8),
('х',	2,	9),
('м',	3,	1),
('у',	3,	2),
('ы',	3,	3),
('ч',	3,	4),
('т',	3,	5),
('с',	3,	6),
('р',	3,	7),
('б',	3,	8),
('н',	3,	9),
('а',	4,	1),
('п',	4,	2),
('м',	4,	3),
('л',	4,	4),
('е',	4,	5),
('н',	4,	6),
('г',	4,	7),
('е',	4,	8),
('ы',	4,	9),
('т',	5,	1),
('р',	5,	2),
('а',	5,	3),
('в',	5,	4),
('щ',	5,	5),
('д',	5,	6),
('л',	5,	7),
('х',	5,	8),
('й',	5,	9),
('с',	6,	1),
('ы',	6,	2),
('ш',	6,	3),
('и',	6,	4),
('н',	6,	5),
('й',	6,	6),
('м',	6,	7),
('в',	6,	8),
('а',	6,	9),
('о',	7,	1),
('к',	7,	2),
('и',	7,	3),
('н',	7,	4),
('а',	7,	5),
('е',	7,	6),
('т',	7,	7),
('ю',	7,	8),
('к',	7,	9),
('у',	8,	1),
('н',	8,	2),
('г',	8,	3),
('у',	8,	4),
('ъ',	8,	5),
('р',	8,	6),
('б',	8,	7),
('ь',	8,	8),
('е',	8,	9),
('к',	9,	1),
('р',	9,	2),
('с',	9,	3),
('о',	9,	4),
('м',	9,	5),
('ь',	9,	6),
('л',	9,	7),
('п',	9,	8),
('р',	9,	9),
('т',	10,	1),
('г',	10,	2),
('о',	10,	3),
('л',	10,	4),
('щ',	10,	5),
('к',	10,	6),
('о',	10,	7),
('м',	10,	8),
('ч',	10,	9),
('в',	11,	1),
('м',	11,	2),
('д',	11,	3),
('ы',	11,	4),
('е',	11,	5),
('ч',	11,	6),
('л',	11,	7),
('й',	11,	8),
('с',	11,	9),
('р',	12,	1),
('а',	12,	2),
('н',	12,	3),
('н',	12,	4),
('й',	12,	5),
('н',	12,	6),
('и',	12,	7),
('ч',	12,	8),
('д',	12,	9),
('о',	13,	1),
('д',	13,	2),
('н',	13,	3),
('р',	13,	4),
('е',	13,	5),
('т',	13,	6),
('а',	13,	7),
('ы',	13,	8),
('и',	13,	9),
('ш',	14,	1),
('з',	14,	2),
('е',	14,	3),
('т',	14,	4),
('и',	14,	5),
('х',	14,	6),
('о',	14,	7),
('з',	14,	8),
('ю',	14,	9);

-- 2018-05-23 09:20:29