-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 28 Kwi 2018, 17:06
-- Wersja serwera: 10.1.31-MariaDB
-- Wersja PHP: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `chat`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_date` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `message`
--

INSERT INTO `message` (`id`, `description`, `input_date`, `author`, `status`, `info`) VALUES
(18, 'witam wszystkich na czacie', '2018-04-28 14:14:21', 'qweqwe', 'accepted', NULL),
(19, 'to jest wiadomość', '2018-04-28 12:11:24', 'qweqwe(ekspert)', 'accepted', NULL),
(20, 'kolejna wiadomość', '2018-04-28 12:11:54', 'adam(moderator)', 'accepted', NULL),
(21, 'kolejna', '2018-04-28 12:12:57', 'adam(moderator)', 'accepted', NULL),
(22, 'jeszcze jedna', '2018-04-28 12:14:06', 'adam(moderator)', 'accepted', NULL),
(23, 'to jest moja wiadomość', '2018-04-28 13:43:02', 'qweqwe', 'accepted', NULL),
(24, 'dodaję wiadomość', '2018-04-28 13:44:03', 'adam(ekspert)', 'accepted', NULL),
(25, 'wiadomośc od adama', '2018-04-28 13:44:29', 'adam', 'accepted', NULL),
(26, 'treść', '2018-04-28 14:15:05', 'qweqwe', 'accepted', NULL),
(27, 'wiadmość', '2018-04-28 15:05:59', 'witam', 'accepted', NULL),
(28, 'wiadomośćć', '2018-04-28 15:05:53', 'witam', 'accepted', NULL),
(29, 'wiadomość asd', '2018-04-28 15:10:01', 'roman', 'accepted', NULL),
(30, 'pisze cos', '2018-04-28 15:10:02', 'roman', 'accepted', NULL),
(31, 'pisze', '2018-04-28 15:09:59', 'roman', 'accepted', NULL),
(32, 'asd', '2018-04-28 15:09:57', 'roman', 'accepted', NULL),
(33, 'opop', '2018-04-28 15:11:41', 'adam(ekspert)', 'accepted', NULL),
(35, 'pisze cos', '2018-04-28 15:16:13', 'adam', 'accepted', NULL),
(39, 'adam', '2018-04-28 15:27:16', 'adam', 'accepted', NULL),
(42, 'witam', '2018-04-28 15:37:16', 'adam(ekspert)', 'accepted', NULL),
(45, 'eeee', '2018-04-28 16:03:17', 'adam', 'accepted', NULL),
(46, 'witam', '2018-04-28 16:15:14', 'adam', 'accepted', NULL),
(49, 'witam', '2018-04-28 17:04:55', 'adam', 'accepted', NULL),
(50, 'to ja', '2018-04-28 17:04:54', 'adam', 'accepted', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `migration_versions`
--

INSERT INTO `migration_versions` (`version`) VALUES
('20180427174325'),
('20180428132840');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
