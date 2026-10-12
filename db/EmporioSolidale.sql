-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Giu 02, 2021 alle 23:09
-- Versione del server: 10.4.17-MariaDB
-- Versione PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `EmporioSolidale`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Administrator`
--

CREATE TABLE `Administrator` (
  `Username` varchar(15) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Administrator`
--

INSERT INTO `Administrator` (`Username`, `Password`) VALUES
('emporiosolidale', '$2y$12$FU6AcI57V/.0oH9moZSGzeF8BiTahpHuvuTqfYA9jCoJGmYRgZVyO');

-- --------------------------------------------------------

--
-- Struttura della tabella `Competenze`
--

CREATE TABLE `Competenze` (
  `Nome` varchar(30) NOT NULL,
  `Descrizione` tinytext DEFAULT NULL,
  `Presso` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `competenzeview`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `competenzeview` (
`nMembro` int(2)
,`NomeCognome` varchar(60)
,`LuogoNascita` varchar(30)
,`DataNascita` varchar(10)
,`Parentela` varchar(20)
,`Occupazione` varchar(20)
,`TitoloDiStudio` varchar(20)
,`Competenze` varchar(20)
,`ConoscenzaLingua` varchar(30)
,`Patente` varchar(4)
,`AttualeAtt` varchar(15)
,`Presso` varchar(15)
,`Termine` date
,`SenzaLavoroDa` date
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `confirmation`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `confirmation` (
`Cod_Fiscale` char(16)
,`Cognome` varchar(30)
,`Nome` varchar(30)
,`nMembri` int(2)
,`PunteggioISEE` int(11)
,`PunteggioPresenzaMinori` int(11)
,`PunteggioDisoccupazione` int(11)
,`PunteggioInvalidi` int(11)
,`PunteggioSituazioneDebitoria` int(11)
,`PunteggioBenefit` int(11)
,`PunteggioTotale` int(11)
,`CodiceTessera` char(12)
,`DataOraRilascioTessera` varchar(10)
,`DataOraScadenzaTessera` varchar(10)
,`DataOraRitiroTessera` varchar(10)
,`DataOraStalloTessera` varchar(10)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `DispCollaborative`
--

CREATE TABLE `DispCollaborative` (
  `CodFamiglia` char(16) NOT NULL,
  `AmbitoFormativo` tinytext DEFAULT NULL,
  `AmbitoLavorativo` tinytext DEFAULT NULL,
  `AmbitoSociale` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `DispCollaborative`
--

INSERT INTO `DispCollaborative` (`CodFamiglia`, `AmbitoFormativo`, `AmbitoLavorativo`, `AmbitoSociale`) VALUES
('JNVSJNKFJNKERJNT', '', '', ''),
('JRRBCT96D19F205U', 'Disponibile per insegnare', 'Disp. a lavorare', 'Disp a socializzare'),
('JRRBCT96D19F205Y', 'Disponibile per insegnare', 'Disp. a lavorare', 'Disp a socializzare');

-- --------------------------------------------------------

--
-- Struttura della tabella `Entrate`
--

CREATE TABLE `Entrate` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int(11) NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Entrate`
--

INSERT INTO `Entrate` (`CodFamiglia`, `N`, `Elemento`, `Valore`, `Scadenza`) VALUES
('JRRBCT96D19F205U', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 3, '3333', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 3, '3333', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 4, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 4, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 5, '5555', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 5, '5555', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 6, '6666', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 6, '6666', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 7, '7777', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 7, '7777', '444.00', '2000-01-01');

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `export`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `export` (
`Cod_Fiscale` char(16)
,`Cognome` varchar(30)
,`Nome` varchar(30)
,`Cittadinanza` varchar(15)
,`LuogoNascita` varchar(30)
,`DataNascita` date
,`TipoAlloggio` varchar(30)
,`Residenza` varchar(30)
,`Via` varchar(30)
,`nMembri` int(2)
,`Telefono` varchar(15)
,`Mail` varchar(100)
,`PunteggioISEE` int(11)
,`PunteggioPresenzaMinori` int(11)
,`PunteggioDisoccupazione` int(11)
,`PunteggioInvalidi` int(11)
,`PunteggioSituazioneDebitoria` int(11)
,`PunteggioBenefit` int(11)
,`PunteggioTotale` int(11)
,`CodiceTessera` char(12)
,`DataOraRilascioTessera` varchar(10)
,`DataOraScadenzaTessera` varchar(10)
,`DataOraRitiroTessera` varchar(10)
,`DataOraStalloTessera` varchar(10)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `graduatoria`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `graduatoria` (
`Cod_Fiscale` char(16)
,`Cognome` varchar(30)
,`Nome` varchar(30)
,`nMembri` int(2)
,`Telefono` varchar(15)
,`Mail` varchar(100)
,`PunteggioISEE` int(11)
,`PunteggioPresenzaMinori` int(11)
,`PunteggioDisoccupazione` int(11)
,`PunteggioInvalidi` int(11)
,`PunteggioSituazioneDebitoria` int(11)
,`PunteggioBenefit` int(11)
,`PunteggioTotale` int(11)
,`CodiceTessera` char(12)
,`DataOraRilascioTessera` varchar(10)
,`DataOraScadenzaTessera` varchar(10)
,`DataOraRitiroTessera` varchar(10)
,`DataOraStalloTessera` varchar(10)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `IndicatoriISEE`
--

CREATE TABLE `IndicatoriISEE` (
  `N` int(11) NOT NULL,
  `ISEEmin` int(11) NOT NULL,
  `ISEEmax` int(11) NOT NULL,
  `Punteggio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `IndicatoriISEE`
--

INSERT INTO `IndicatoriISEE` (`N`, `ISEEmin`, `ISEEmax`, `Punteggio`) VALUES
(1, 0, 1500, 100),
(2, 1501, 3000, 80),
(3, 3001, 4500, 60),
(4, 4501, 6000, 40),
(5, 6001, 7500, 20);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionidispcoll`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionidispcoll` (
`AmbitoFormativo` tinytext
,`AmbitoLavorativo` tinytext
,`AmbitoSociale` tinytext
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionientrate`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionientrate` (
`N` int(11)
,`Elemento` varchar(60)
,`Valore` decimal(8,2)
,`Scadenza` varchar(10)
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionimembri`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionimembri` (
`nMembro` int(2)
,`NomeCognome` varchar(60)
,`LuogoNascita` varchar(30)
,`DataNascita` varchar(10)
,`Parentela` varchar(20)
,`Occupazione` varchar(20)
,`TitoloDiStudio` varchar(20)
,`Competenze` varchar(20)
,`ConoscenzaLingua` varchar(30)
,`Patente` varchar(4)
,`AttualeAtt` varchar(15)
,`Presso` varchar(15)
,`Termine` date
,`SenzaLavoroDa` date
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionipatratt`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionipatratt` (
`N` int(11)
,`Elemento` varchar(60)
,`Valore` decimal(8,2)
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionipatrpass`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionipatrpass` (
`N` int(11)
,`Elemento` varchar(60)
,`Valore` decimal(8,2)
,`Scadenza` varchar(10)
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionirichiedente`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionirichiedente` (
`Cod_Fiscale` char(16)
,`Cognome` varchar(30)
,`Nome` varchar(30)
,`LuogoNascita` varchar(30)
,`DataNascita` varchar(10)
,`Cittadinanza` varchar(15)
,`InItaliaDallAnno` year(4)
,`Residenza` varchar(30)
,`Via` varchar(30)
,`TipoAlloggio` varchar(30)
,`DocumentoIdent` char(9)
,`DocumentoImmig` char(10)
,`Telefono` varchar(15)
,`Mail` varchar(100)
,`ServiziSociali` varchar(15)
,`ComeHaConosciutoEmporio` varchar(30)
,`nMembri` int(2)
,`ISEE` int(11)
,`CodiceTessera` char(12)
,`DataOraRilascioTessera` varchar(10)
,`DataOraScadenzaTessera` varchar(10)
,`DataOraRitiroTessera` varchar(10)
,`DataOraStalloTessera` varchar(10)
,`PunteggioISEE` int(11)
,`PunteggioPresenzaMinori` int(11)
,`PunteggioDisoccupazione` int(11)
,`PunteggioInvalidi` int(11)
,`PunteggioSituazioneDebitoria` int(11)
,`PunteggioBenefit` int(11)
,`PunteggioTotale` int(11)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazionisss`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazionisss` (
`N` int(11)
,`Evento` varchar(60)
,`Anno` year(4)
,`DocAll` varchar(30)
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `informazioniuscite`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `informazioniuscite` (
`N` int(11)
,`Elemento` varchar(60)
,`Valore` decimal(8,2)
,`Scadenza` varchar(10)
,`CodFamiglia` char(16)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `Membri`
--

CREATE TABLE `Membri` (
  `nMembro` int(2) NOT NULL,
  `NomeCognome` varchar(60) DEFAULT NULL,
  `LuogoNascita` varchar(30) DEFAULT NULL,
  `DataNascita` date DEFAULT NULL,
  `Parentela` varchar(20) NOT NULL,
  `Occupazione` varchar(20) DEFAULT NULL,
  `TitoloDiStudio` varchar(20) DEFAULT NULL,
  `Competenze` varchar(20) DEFAULT NULL,
  `ConoscenzaLingua` varchar(30) DEFAULT NULL,
  `Patente` varchar(4) DEFAULT NULL,
  `AttualeAtt` varchar(15) DEFAULT NULL,
  `Presso` varchar(15) DEFAULT NULL,
  `Termine` date DEFAULT NULL,
  `SenzaLavoroDa` date DEFAULT NULL,
  `CodFamiglia` char(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Membri`
--

INSERT INTO `Membri` (`nMembro`, `NomeCognome`, `LuogoNascita`, `DataNascita`, `Parentela`, `Occupazione`, `TitoloDiStudio`, `Competenze`, `ConoscenzaLingua`, `Patente`, `AttualeAtt`, `Presso`, `Termine`, `SenzaLavoroDa`, `CodFamiglia`) VALUES
(1, 'Jerry Biscotti', 'Milano', '1988-04-01', 'Padre', 'Conduttore', 'Scuole Superiori', NULL, NULL, 'B2', NULL, NULL, NULL, '2021-05-05', 'JRRBCT96D19F205U'),
(1, 'Jerry Biscotti', 'Milano', '1988-04-01', 'Padre', 'Conduttore', 'Scuole Superiori', NULL, NULL, 'B2', NULL, NULL, NULL, '2018-02-02', 'JRRBCT96D19F205Y'),
(2, 'Alexandra Rotterdelli', 'Milano', '1988-04-01', 'Madre', 'Cassiera', 'Medie', NULL, NULL, 'A1', NULL, NULL, NULL, '2021-02-05', 'JRRBCT96D19F205U'),
(2, 'Alexandra Rotterdelli', 'Milano', '1988-04-01', 'Madre', 'Cassiera', 'Medie', NULL, NULL, 'A1', NULL, NULL, NULL, '2010-10-10', 'JRRBCT96D19F205Y'),
(3, 'Gianni Depp', 'Milano', '1988-04-01', 'Figlio', NULL, 'Elementari', NULL, 'Inglese', NULL, 'Irrigatore', 'Milano', NULL, NULL, 'JRRBCT96D19F205U'),
(3, 'Gianni Depp', 'Milano', '1988-04-01', 'Figlio', NULL, 'Elementari', NULL, 'Inglese', NULL, 'Irrigatore', 'Milano', NULL, NULL, 'JRRBCT96D19F205Y');

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `pass`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `pass` (
`Cod_Fiscale` char(16)
,`Cognome` varchar(30)
,`Nome` varchar(30)
,`nMembri` int(2)
,`PunteggioISEE` int(11)
,`PunteggioPresenzaMinori` int(11)
,`PunteggioDisoccupazione` int(11)
,`PunteggioInvalidi` int(11)
,`PunteggioSituazioneDebitoria` int(11)
,`PunteggioBenefit` int(11)
,`PunteggioTotale` int(11)
,`CodiceTessera` char(12)
,`DataOraRilascioTessera` varchar(10)
,`DataOraScadenzaTessera` varchar(10)
,`DataOraRitiroTessera` varchar(10)
,`DataOraStalloTessera` varchar(10)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `PatrAtt`
--

CREATE TABLE `PatrAtt` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int(11) NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `PatrAtt`
--

INSERT INTO `PatrAtt` (`CodFamiglia`, `N`, `Elemento`, `Valore`) VALUES
('JRRBCT96D19F205U', 1, '1111', '34523.00'),
('JRRBCT96D19F205Y', 1, '1111', '34523.00'),
('JRRBCT96D19F205U', 2, '2222', '65432.00'),
('JRRBCT96D19F205Y', 2, '2222', '65432.00'),
('JRRBCT96D19F205U', 3, '4444', '34547.00'),
('JRRBCT96D19F205Y', 3, '4444', '34547.00'),
('JRRBCT96D19F205U', 4, '7777', '5346.00'),
('JRRBCT96D19F205Y', 4, '7777', '5346.00');

-- --------------------------------------------------------

--
-- Struttura della tabella `PatrPass`
--

CREATE TABLE `PatrPass` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int(11) NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `PatrPass`
--

INSERT INTO `PatrPass` (`CodFamiglia`, `N`, `Elemento`, `Valore`, `Scadenza`) VALUES
('JRRBCT96D19F205U', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 3, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 3, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 4, '7777', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 4, '7777', '444.00', '2000-01-01');

-- --------------------------------------------------------

--
-- Struttura della tabella `Punteggio`
--

CREATE TABLE `Punteggio` (
  `CodFamiglia` char(16) NOT NULL,
  `PunteggioISEE` int(11) DEFAULT 0,
  `PunteggioPresenzaMinori` int(11) DEFAULT 0,
  `PunteggioDisoccupazione` int(11) DEFAULT 0,
  `PunteggioInvalidi` int(11) DEFAULT 0,
  `PunteggioSituazioneDebitoria` int(11) DEFAULT 0,
  `PunteggioBenefit` int(11) DEFAULT 0,
  `PunteggioTotale` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Punteggio`
--

INSERT INTO `Punteggio` (`CodFamiglia`, `PunteggioISEE`, `PunteggioPresenzaMinori`, `PunteggioDisoccupazione`, `PunteggioInvalidi`, `PunteggioSituazioneDebitoria`, `PunteggioBenefit`, `PunteggioTotale`) VALUES
('BGLLDA88D01G779J', 40, 15, 15, 15, 15, -10, 90),
('BNDBDR88D01D872J', 80, 30, 30, 30, 30, -30, 170),
('BNVLTR63D22B880W', 40, 10, 30, 20, 50, -30, 120),
('CNSSRA88D01G779L', 40, 15, 15, 15, 15, -10, 90),
('RSSMRA80A01A944X', 0, 0, 0, 0, 0, 0, 0),
('RSSMRA80A01A944Y', 0, 0, 0, 0, 0, 0, 0),
('CRSGDU88D01G779W', 60, 30, 30, 30, 30, -30, 150),
('CRTRCE88D01D643V', 0, 50, 50, 50, 50, -40, 160),
('DFRPGR88D01G779W', 80, 50, 50, 50, 50, -40, 240),
('FRHLRB82D16B880K', 60, 30, 30, 30, 30, -30, 150),
('FRJHMD88D01G779I', 60, 50, 50, 50, 50, -40, 160),
('FRNFNL85D15B157Y', 60, 140, 100, 40, 5, -20, 325),
('FRNMDR66P12C573P', 60, 140, 200, 45, 5, -10, 440),
('FRSMMD88D01B880I', 20, 15, 15, 15, 15, -10, 70),
('FRYPLP88D01D872A', 100, 50, 50, 50, 50, -40, 260),
('GDFGDFGDFDGDFGFG', 80, 0, 35, 0, 0, 0, 115),
('GNNFNZ63D22B880D', 20, 10, 20, 0, 5, -35, 20),
('GRCBRN88D01G779O', 100, 15, 15, 15, 15, -10, 150),
('GRGGRG73D16B880J', 80, 15, 15, 15, 15, -10, 70),
('HSSHCM88D01G779A', 80, 40, 30, 20, 10, -20, 160),
('JNVSJNKFJNKERJNT', 80, 0, 35, 0, 0, 0, 115),
('JRRBCT96D19F205U', 80, 10, 20, 15, 10, -5, 130),
('JRRBCT96D19F205Y', 80, 5, 5, 5, 5, -15, 75),
('KHLHSN88D01G779A', 100, 10, 30, 20, 45, -40, 165),
('LBAGDY88D01G779Y', 60, 50, 50, 50, 50, -40, 160),
('LCKMMM88D01B880G', 40, 30, 30, 30, 30, -30, 150),
('LKKRML88D01L049D', 0, 50, 50, 50, 50, -40, 160),
('LMPDRA88D01G779L', 20, 30, 30, 30, 30, -30, 110),
('LRNBTC92L12I158T', 80, 30, 50, 110, 5, -50, 225),
('LRTJRG88D01B880R', 40, 50, 50, 50, 50, -40, 160),
('MHMJRB94H09L736O', 100, 20, 20, 20, 5, -5, 160),
('MHRMRD88D01G779L', 80, 10, 10, 10, 5, -5, 110),
('MLNCSM88D01G779E', 80, 30, 30, 30, 30, -30, 180),
('MLSBST72A44G273B', 80, 120, 140, 60, 100, 0, 500),
('MNARZK85H13B880E', 80, 10, 40, 30, 30, -40, 150),
('MNCNDR88D01G779C', 40, 50, 50, 50, 50, -40, 200),
('MRARSS99T12A089H', 40, 170, 20, 40, 5, -5, 270),
('MRTNST72A44G942G', 40, 100, 100, 210, 5, -55, 200),
('MSSGPP88D01G779B', 60, 15, 15, 15, 15, -10, 110),
('NDALRB85M57A662E', 60, 10, 10, 10, 5, -10, 25),
('NRBPVN36L04B710R', 100, 80, 120, 70, 50, -20, 400),
('PNZPLT88D01F257N', 100, 50, 50, 50, 50, -40, 260),
('PPYPPY88D01M183P', 100, 50, 50, 50, 50, -40, 260),
('PRNNOE66D16B880L', 100, 10, 30, 60, 50, -10, 240),
('PTTBRD64T12A944R', 0, 50, 50, 50, 50, -40, 160),
('RBRRRT88D01G779Q', 20, 10, 10, 20, 40, -10, 90),
('RNAGNN88D01G779Q', 40, 140, 0, 10, 5, -10, 185),
('SLMLKM82D16B880J', 80, 0, 10, 10, 20, -10, 110),
('SMLBDL88D01G779C', 60, 140, 30, 20, 5, -25, 230),
('SMNLNZ79M41A944A', 20, 80, 20, 20, 10, -10, 140),
('SPHTNR88D22B880O', 40, 70, 20, 15, 25, -20, 150),
('SRMBHR80D16B880X', 60, 40, 0, 0, 5, -5, 100);

-- --------------------------------------------------------

--
-- Struttura della tabella `PunteggioMensile`
--

CREATE TABLE `PunteggioMensile` (
  `N` int(11) NOT NULL,
  `nComponenti` int(11) NOT NULL,
  `ValoreEuro` decimal(5,2) NOT NULL,
  `ValorePunti` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `PunteggioMensile`
--

INSERT INTO `PunteggioMensile` (`N`, `nComponenti`, `ValoreEuro`, `ValorePunti`) VALUES
(1, 1, '150.00', 300),
(2, 2, '200.00', 400),
(3, 3, '250.00', 500),
(4, 4, '300.00', 600);

-- --------------------------------------------------------

--
-- Struttura della tabella `Richiedente`
--

CREATE TABLE `Richiedente` (
  `Cod_Fiscale` char(16) NOT NULL,
  `Cognome` varchar(30) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `LuogoNascita` varchar(30) NOT NULL,
  `DataNascita` date NOT NULL,
  `Cittadinanza` varchar(15) NOT NULL,
  `InItaliaDallAnno` year(4) DEFAULT NULL,
  `Residenza` varchar(30) NOT NULL,
  `Via` varchar(30) NOT NULL,
  `TipoAlloggio` varchar(30) NOT NULL,
  `DocumentoIdent` char(9) DEFAULT NULL,
  `DocumentoImmig` char(10) DEFAULT NULL,
  `Telefono` varchar(15) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `ServiziSociali` varchar(15) NOT NULL,
  `ComeHaConosciutoEmporio` varchar(30) DEFAULT NULL,
  `nMembri` int(2) NOT NULL,
  `DataOraRitiroTessera` date DEFAULT NULL,
  `DataOraStalloTessera` date DEFAULT NULL,
  `ISEE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Richiedente`
--

INSERT INTO `Richiedente` (`Cod_Fiscale`, `Cognome`, `Nome`, `LuogoNascita`, `DataNascita`, `Cittadinanza`, `InItaliaDallAnno`, `Residenza`, `Via`, `TipoAlloggio`, `DocumentoIdent`, `DocumentoImmig`, `Telefono`, `Mail`, `ServiziSociali`, `ComeHaConosciutoEmporio`, `nMembri`, `DataOraRitiroTessera`, `DataOraStalloTessera`, `ISEE`) VALUES
('BGLLDA88D01G779J', 'Baglio', 'Aldo', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000001', 'aldobaglio@example.com', 'No', NULL, 3, NULL, NULL, 6000),
('BNDBDR88D01D872J', 'Bending Rodriguez', 'Bender', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000002', 'bender@example.com', 'No', NULL, 6, NULL, NULL, 2000),
('BNVLTR63D22B880W', 'Lotaro', 'Benevolo', 'CASALECCHIO DI RENO', '1963-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000003', 'malevololotaro@example.com', 'No', NULL, 10, NULL, NULL, 6000),
('CNSSRA88D01G779L', 'Cinesca', 'Sara', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000004', 'saracinesca@example.com', 'No', NULL, 9, NULL, NULL, 6000),
('RSSMRA80A01A944X', 'Rossi', 'Mario', 'Bologna', '1980-01-01', 'ITA', NULL, 'Bologna', 'Via Esempio 1', 'Casetta', '123456789', NULL, '3330000005', 'mario.rossi1@example.com', 'No', NULL, 5, NULL, NULL, 44444),
('RSSMRA80A01A944Y', 'Rossi', 'Mario', 'Bologna', '1980-01-01', 'ITA', NULL, 'Bologna', 'Via Esempio 1', 'Casetta', '123456789', NULL, '3330000005', 'mario.rossi2@example.com', 'No', NULL, 5, NULL, NULL, 44444),
('CRSGDU88D01G779W', 'Caruso', 'Guido', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000006', 'simsimi@example.com', 'No', NULL, 8, NULL, NULL, 4000),
('CRTRCE88D01D643V', 'Cartman', 'Eric', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000007', 'fatcartman@example.com', 'No', NULL, 10, NULL, NULL, 15000),
('DFRPGR88D01G779W', 'Odifreddi', 'Piergiorgio', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000008', 'mariz@example.com', 'No', NULL, 8, NULL, NULL, 2000),
('FRHLRB82D16B880K', 'Farhad', 'Lahrib', 'CASALECCHIO DI RENO', '1982-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000009', 'huntersyler@example.com', 'No', NULL, 5, NULL, NULL, 4000),
('FRJHMD88D01G779I', 'Farajhad', 'Ahmed', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000010', 'supernova44@example.com', 'No', NULL, 5, NULL, NULL, 4000),
('FRNFNL85D15B157Y', 'Frenulo', 'Francesco', 'Brescia', '1985-04-15', 'ITA', NULL, 'Brescia', 'Via Uffizzi 11', 'Popolare', '123456789', NULL, '3330000011', 'frafresnu@example.com', 'No', NULL, 3, NULL, NULL, 4000),
('FRNMDR66P12C573P', 'Amadori', 'Francesco', 'Cesena', '1966-09-12', 'ITA', NULL, 'Cesena', 'Lungo Tevere 12', 'Popolare', '123456789', NULL, '3330000012', 'amafran@example.com', 'No', NULL, 7, NULL, NULL, 4000),
('FRSMMD88D01B880I', 'Fareshi', 'Mohamed', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000013', 'mohamoha@example.com', 'No', NULL, 6, NULL, NULL, 7000),
('FRYPLP88D01D872A', 'Fry', 'Philipp J.', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000014', 'fry@example.com', 'No', NULL, 3, NULL, NULL, 1000),
('GDFGDFGDFDGDFGFG', 'Gfdfgdgd', 'Gfdgfdgfdfg', 'Dfgdgd', '2021-02-06', 'Fdgfdgf', NULL, 'Fggfdfg', 'Gfddfdf', '', '', '', 'dsfdsf', 'dsfdfsfds@fdsdfd.f', '', '', 1, NULL, NULL, 2000),
('GNNFNZ63D22B880D', 'Fetonzi', 'Gianni', 'CASALECCHIO DI RENO', '1963-02-10', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000015', 'giannibislacchi@example.com', 'No', NULL, 10, NULL, NULL, 7000),
('GRCBRN88D01G779O', 'Greco', 'Bruno', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000016', 'giolte@example.com', 'No', NULL, 4, NULL, NULL, 0),
('GRGGRG73D16B880J', 'Giorgioni', 'Giorgio', 'CASALECCHIO DI RENO', '1973-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000017', 'joegiojoe@example.com', 'No', NULL, 4, NULL, NULL, 2000),
('HSSHCM88D01G779A', 'Hossein', 'Hoscem', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000018', 'superman6@example.com', 'No', NULL, 7, NULL, NULL, 2000),
('JNVSJNKFJNKERJNT', 'Adsas', 'Sdassd', 'Ddadsad', '1970-01-01', 'Sda', NULL, 'Sad', 'Sda', '', '', '', '43i499if0i', 'nxknnkjkjn@example.com', '', '', 1, NULL, NULL, 2000),
('JRRBCT96D19F205U', 'Biscotti', 'Jerry', 'Milano', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000019', 'jerrybiscuits@example.com', 'No', NULL, 3, NULL, NULL, 2000),
('JRRBCT96D19F205Y', 'Biscotti', 'Jerry', 'Milano', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000005', 'biscuitsgarrick@example.com', 'No', NULL, 3, NULL, NULL, 2000),
('KHLHSN88D01G779A', 'Khaled', 'Hossein', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000020', 'ilcacciatoreaquiloni@example.com', 'No', NULL, 12, NULL, NULL, 0),
('LBAGDY88D01G779Y', 'Alba', 'Gordy', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000021', 'porter@example.com', 'No', NULL, 7, NULL, NULL, 4000),
('LCKMMM88D01B880G', 'Lackuft', 'Muhammad', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000022', 'mahmood@example.com', 'No', NULL, 5, NULL, NULL, 6000),
('LKKRML88D01L049D', 'Lukaku', 'Romelu', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000023', 'lookukoo@example.com', 'No', NULL, 11, NULL, NULL, 10000),
('LMPDRA88D01G779L', 'Lampa', 'Dario', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000024', 'lampadario@example.com', 'No', NULL, 12, NULL, NULL, 7000),
('LRNBTC92L12I158T', 'Botticelli', 'Lorenzo', 'San Severo', '1992-07-12', 'ITA', NULL, 'San Severo', 'Via Opera 11', 'Popolare', '123456789', NULL, '3330000025', 'lollo.er.botte@example.com', 'No', NULL, 2, NULL, NULL, 2000),
('LRTJRG88D01B880R', 'Lorathio', 'Jorge', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000026', 'jorgeh@example.com', 'No', NULL, 5, NULL, NULL, 6000),
('MHMJRB94H09L736O', 'Jairiby', 'Mohamed', 'Venezia', '1994-09-11', 'ITA', NULL, 'Venezia', 'Via del poretto 33', 'Popolare', '123456789', NULL, '3330000005', 'momoj@example.com', 'No', NULL, 9, NULL, NULL, 0),
('MHRMRD88D01G779L', 'Mehrid', 'Mehrad', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000027', 'serahmecc@example.com', 'No', NULL, 8, NULL, NULL, 2000),
('MLNCSM88D01G779E', 'Milano', 'Cosimo', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000028', 'fater@example.com', 'No', NULL, 7, NULL, NULL, 2000),
('MLSBST72A44G273B', 'Balestri', 'Melissa', 'Palermo', '1972-09-11', 'ITA', NULL, 'Palermo', 'Via dei Roveri 56', 'Popolare', '123456789', NULL, '3330000029', 'ballalissa@example.com', 'No', NULL, 8, NULL, NULL, 2000),
('MNARZK85H13B880E', 'Ouruzka', 'Amine', 'CASALECCHIO DI RENO', '1985-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000030', 'amineazimer@example.com', 'No', NULL, 12, NULL, NULL, 2000),
('MNCNDR88D01G779C', 'Mancini', 'Andrea', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000031', 'ilmancio@example.com', 'No', NULL, 14, NULL, NULL, 6000),
('MRARSS99T12A089H', 'Rossi', 'Mario', 'Agrigento', '1999-12-12', 'ITA', NULL, 'Agrigento', 'Via Paradiso 12', 'Popolare', '123456789', NULL, '3330000032', 'mariobros@example.com', 'No', NULL, 4, NULL, NULL, 6000),
('MRTNST72A44G942G', 'Angusti', 'Martina', 'Potenza', '1972-01-04', 'ITA', NULL, 'Potenza', 'Via Giornalisti 44', 'Popolare', '123456789', NULL, '3330000033', 'marangus@example.com', 'No', NULL, 6, NULL, NULL, 6000),
('MSSGPP88D01G779B', 'Musso', 'Giuseppe', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000034', 'mousse@example.com', 'No', NULL, 8, NULL, NULL, 4000),
('NDALRB85M57A662E', 'Lahrib', 'Nadia', 'CASALECCHIO DI RENO', '1985-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000035', 'ndanda@example.com', 'No', NULL, 4, NULL, NULL, 4000),
('NRBPVN36L04B710R', 'Padovano', 'Norberto', 'Caprino Bergamasco', '1936-07-04', 'ITA', NULL, 'Caprino Bergamasco', 'Strada Provinciale 65, 101', 'Popolare', 'WN59274PO', NULL, '3330000036', 'norbertopadovano@example.com', 'No', NULL, 12, NULL, NULL, 0),
('PNZPLT88D01F257N', 'Pelato', 'Ponzio', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000037', 'ponzioilpelato@example.com', 'No', NULL, 7, NULL, NULL, 1000),
('PPYPPY88D01M183P', 'Popeye', 'Popeye', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000038', 'tarminaz@example.com', 'No', NULL, 4, NULL, NULL, 1000),
('PRNNOE66D16B880L', 'Purina', 'One', 'CASALECCHIO DI RENO', '1966-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000039', 'purinaone@example.com', 'No', NULL, 4, NULL, NULL, 0),
('PTTBRD64T12A944R', 'Pitt', 'Brad', 'CASALECCHIO DI RENO', '1964-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000040', 'pezzodibread@example.com', 'No', NULL, 9, NULL, NULL, 11000),
('RBRRRT88D01G779Q', 'Roberti', 'Roberto', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000041', 'rob.erto@example.com', 'No', NULL, 2, NULL, NULL, 7000),
('RNAGNN88D01G779Q', 'Rana', 'Giovanni', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000042', 'raaana@example.com', 'No', NULL, 6, NULL, NULL, 6000),
('SLMLKM82D16B880J', 'Olohakim', 'Salim', 'CASALECCHIO DI RENO', '1982-06-15', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000043', 'poolsalah@example.com', 'No', NULL, 9, NULL, NULL, 2000),
('SMLBDL88D01G779C', 'Samaluz', 'Abdul', 'CASALECCHIO DI RENO', '1988-04-01', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000044', 'fighter89@example.com', 'No', NULL, 5, NULL, NULL, 4000),
('SMNLNZ79M41A944A', 'Lorenzotti', 'Samantha', 'Bologna', '1979-08-01', 'ITA', NULL, 'Bologna', 'Via Indipendenza 16', 'Popolare', '123456789', NULL, '3330000045', 'samporenzi@example.com', 'No', NULL, 5, NULL, NULL, 7000),
('SPHTNR88D22B880O', 'Turner', 'Sophie', 'CASALECCHIO DI RENO', '1988-08-16', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000046', 'sofia@example.com', 'No', NULL, 5, NULL, NULL, 6000),
('SRMBHR80D16B880X', 'Ben Hur', 'Surim', 'CASALECCHIO DI RENO', '1980-09-11', 'ITA', NULL, 'CASALECCHIO DI RENO', 'Via Dei Colli', 'Popolare', '123456789', NULL, '3330000047', 'ilgiudeo@example.com', 'No', NULL, 9, NULL, NULL, 4000);

-- --------------------------------------------------------

--
-- Struttura della tabella `SituazioneSocioSanitaria`
--

CREATE TABLE `SituazioneSocioSanitaria` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int(11) NOT NULL,
  `Evento` varchar(60) NOT NULL,
  `Anno` year(4) DEFAULT NULL,
  `DocAll` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `SituazioneSocioSanitaria`
--

INSERT INTO `SituazioneSocioSanitaria` (`CodFamiglia`, `N`, `Evento`, `Anno`, `DocAll`) VALUES
('GDFGDFGDFDGDFGFG', 1, 'Mancanza del partner per...', 2002, 'Dsfsdfsd'),
('JNVSJNKFJNKERJNT', 1, '', 2002, 'Dsfdsfdf'),
('JRRBCT96D19F205U', 1, '1111', 2000, 'adsasd'),
('JRRBCT96D19F205Y', 1, '1111', 2000, 'adsasd'),
('JRRBCT96D19F205U', 2, '2222', 2000, 'dsadas'),
('JRRBCT96D19F205Y', 2, '2222', 2000, 'dsadas'),
('JNVSJNKFJNKERJNT', 3, '', 2002, 'Cnjnjkcjnskscj'),
('JRRBCT96D19F205U', 3, '3333', 2000, 'dasasd'),
('JRRBCT96D19F205Y', 3, '3333', 2000, 'dasasd'),
('JRRBCT96D19F205U', 4, '4444', 2002, 'adsads'),
('JRRBCT96D19F205Y', 4, '4444', 2002, 'adsads'),
('JRRBCT96D19F205U', 5, '5555', 2004, 'adsads'),
('JRRBCT96D19F205Y', 5, '5555', 2004, 'adsads'),
('JRRBCT96D19F205U', 6, '6666', 2004, 'adsads'),
('JRRBCT96D19F205Y', 6, '6666', 2004, 'adsads'),
('JRRBCT96D19F205U', 7, '7777', 2004, 'adsads'),
('JRRBCT96D19F205Y', 7, '7777', 2004, 'adsads'),
('JRRBCT96D19F205U', 8, '8888', 2006, 'adsads'),
('JRRBCT96D19F205Y', 8, '8888', 2006, 'adsads'),
('JRRBCT96D19F205U', 9, '9999', 2004, 'adsads'),
('JRRBCT96D19F205Y', 9, '9999', 2004, 'adsads'),
('JRRBCT96D19F205U', 10, '1444', 2009, 'adsads'),
('JRRBCT96D19F205Y', 10, '1444', 2009, 'adsads'),
('JRRBCT96D19F205U', 11, '1555', 2004, 'adsads'),
('JRRBCT96D19F205Y', 11, '1555', 2004, 'adsads'),
('JRRBCT96D19F205U', 12, '1666', 2007, 'adsasd'),
('JRRBCT96D19F205Y', 12, '1666', 2007, 'adsasd');

-- --------------------------------------------------------

--
-- Struttura della tabella `SkillsMembri`
--

CREATE TABLE `SkillsMembri` (
  `nMembro` int(2) NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `Nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Stallo`
--

CREATE TABLE `Stallo` (
  `nMesiStallo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Stallo`
--

INSERT INTO `Stallo` (`nMesiStallo`) VALUES
(12);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `storicotes`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `storicotes` (
`OrdineCronologico` int(11)
,`CodiceTessera` char(12)
,`CodFamiglia` char(16)
,`Nome` varchar(30)
,`Cognome` varchar(30)
,`DataRilascio` varchar(10)
,`DataRitiro` varchar(10)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `StoricoTessere`
--

CREATE TABLE `StoricoTessere` (
  `OrdineCronologico` int(11) NOT NULL,
  `CodiceTessera` char(12) NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `DataRilascio` date NOT NULL,
  `DataRitiro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Tessera`
--

CREATE TABLE `Tessera` (
  `Codice` char(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Tessera`
--

INSERT INTO `Tessera` (`Codice`) VALUES
('BP18SASA00RE'),
('DF34QWER76QY'),
('EK00YGHW44UL'),
('GH56YFGR04PR'),
('HJ15LKLK45KQ'),
('JJ36YPLE32IT'),
('KO19POPO99VU'),
('LK47YHPO12OK'),
('PL16SUSU89NU'),
('SS22FHWS66LO'),
('YY36HJER44JS');

-- --------------------------------------------------------

--
-- Struttura della tabella `TesseraRilasciata`
--

CREATE TABLE `TesseraRilasciata` (
  `CodiceTessera` char(12) NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `DataOraRilascioTessera` date DEFAULT NULL,
  `DataOraScadenzaTessera` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Uscite`
--

CREATE TABLE `Uscite` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int(11) NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Uscite`
--

INSERT INTO `Uscite` (`CodFamiglia`, `N`, `Elemento`, `Valore`, `Scadenza`) VALUES
('JRRBCT96D19F205U', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 1, '1111', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 2, '2222', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 3, '3333', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 3, '3333', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 4, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 4, '4444', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 5, '5555', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 5, '5555', '444.00', '2000-01-01'),
('JRRBCT96D19F205U', 6, '7777', '444.00', '2000-01-01'),
('JRRBCT96D19F205Y', 6, '7777', '444.00', '2000-01-01');

-- --------------------------------------------------------

--
-- Struttura per vista `competenzeview`
--
DROP TABLE IF EXISTS `competenzeview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`competenzeview`  AS SELECT `emporiosolidale`.`membri`.`nMembro` AS `nMembro`, `emporiosolidale`.`membri`.`NomeCognome` AS `NomeCognome`, `emporiosolidale`.`membri`.`LuogoNascita` AS `LuogoNascita`, date_format(`emporiosolidale`.`membri`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `emporiosolidale`.`membri`.`Parentela` AS `Parentela`, `emporiosolidale`.`membri`.`Occupazione` AS `Occupazione`, `emporiosolidale`.`membri`.`TitoloDiStudio` AS `TitoloDiStudio`, `emporiosolidale`.`membri`.`Competenze` AS `Competenze`, `emporiosolidale`.`membri`.`ConoscenzaLingua` AS `ConoscenzaLingua`, `emporiosolidale`.`membri`.`Patente` AS `Patente`, `emporiosolidale`.`membri`.`AttualeAtt` AS `AttualeAtt`, `emporiosolidale`.`membri`.`Presso` AS `Presso`, `emporiosolidale`.`membri`.`Termine` AS `Termine`, `emporiosolidale`.`membri`.`SenzaLavoroDa` AS `SenzaLavoroDa`, `emporiosolidale`.`membri`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`membri` ORDER BY `emporiosolidale`.`membri`.`CodFamiglia` ASC, `emporiosolidale`.`membri`.`nMembro` ASC ;

-- --------------------------------------------------------

--
-- Struttura per vista `confirmation`
--
DROP TABLE IF EXISTS `confirmation`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`confirmation`  AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%Y/%m/%d') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%Y/%m/%d') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%Y/%m/%d') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%Y/%m/%d') AS `DataOraStalloTessera` FROM (((`emporiosolidale`.`richiedente` `R` left join `emporiosolidale`.`tesserarilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `emporiosolidale`.`tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `emporiosolidale`.`punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC ;

-- --------------------------------------------------------

--
-- Struttura per vista `export`
--
DROP TABLE IF EXISTS `export`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`export`  AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`Cittadinanza` AS `Cittadinanza`, `R`.`LuogoNascita` AS `LuogoNascita`, `R`.`DataNascita` AS `DataNascita`, `R`.`TipoAlloggio` AS `TipoAlloggio`, `R`.`Residenza` AS `Residenza`, `R`.`Via` AS `Via`, `R`.`nMembri` AS `nMembri`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`emporiosolidale`.`richiedente` `R` left join `emporiosolidale`.`tesserarilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `emporiosolidale`.`tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `emporiosolidale`.`punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC ;

-- --------------------------------------------------------

--
-- Struttura per vista `graduatoria`
--
DROP TABLE IF EXISTS `graduatoria`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`graduatoria`  AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`emporiosolidale`.`richiedente` `R` left join `emporiosolidale`.`tesserarilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `emporiosolidale`.`tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `emporiosolidale`.`punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionidispcoll`
--
DROP TABLE IF EXISTS `informazionidispcoll`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionidispcoll`  AS SELECT `emporiosolidale`.`dispcollaborative`.`AmbitoFormativo` AS `AmbitoFormativo`, `emporiosolidale`.`dispcollaborative`.`AmbitoLavorativo` AS `AmbitoLavorativo`, `emporiosolidale`.`dispcollaborative`.`AmbitoSociale` AS `AmbitoSociale`, `emporiosolidale`.`dispcollaborative`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`dispcollaborative` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionientrate`
--
DROP TABLE IF EXISTS `informazionientrate`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionientrate`  AS SELECT `emporiosolidale`.`entrate`.`N` AS `N`, `emporiosolidale`.`entrate`.`Elemento` AS `Elemento`, `emporiosolidale`.`entrate`.`Valore` AS `Valore`, date_format(`emporiosolidale`.`entrate`.`Scadenza`,'%d/%m/%Y') AS `Scadenza`, `emporiosolidale`.`entrate`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`entrate` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionimembri`
--
DROP TABLE IF EXISTS `informazionimembri`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionimembri`  AS SELECT `emporiosolidale`.`membri`.`nMembro` AS `nMembro`, `emporiosolidale`.`membri`.`NomeCognome` AS `NomeCognome`, `emporiosolidale`.`membri`.`LuogoNascita` AS `LuogoNascita`, date_format(`emporiosolidale`.`membri`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `emporiosolidale`.`membri`.`Parentela` AS `Parentela`, `emporiosolidale`.`membri`.`Occupazione` AS `Occupazione`, `emporiosolidale`.`membri`.`TitoloDiStudio` AS `TitoloDiStudio`, `emporiosolidale`.`membri`.`Competenze` AS `Competenze`, `emporiosolidale`.`membri`.`ConoscenzaLingua` AS `ConoscenzaLingua`, `emporiosolidale`.`membri`.`Patente` AS `Patente`, `emporiosolidale`.`membri`.`AttualeAtt` AS `AttualeAtt`, `emporiosolidale`.`membri`.`Presso` AS `Presso`, `emporiosolidale`.`membri`.`Termine` AS `Termine`, `emporiosolidale`.`membri`.`SenzaLavoroDa` AS `SenzaLavoroDa`, `emporiosolidale`.`membri`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`membri` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionipatratt`
--
DROP TABLE IF EXISTS `informazionipatratt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionipatratt`  AS SELECT `emporiosolidale`.`patratt`.`N` AS `N`, `emporiosolidale`.`patratt`.`Elemento` AS `Elemento`, `emporiosolidale`.`patratt`.`Valore` AS `Valore`, `emporiosolidale`.`patratt`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`patratt` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionipatrpass`
--
DROP TABLE IF EXISTS `informazionipatrpass`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionipatrpass`  AS SELECT `emporiosolidale`.`patrpass`.`N` AS `N`, `emporiosolidale`.`patrpass`.`Elemento` AS `Elemento`, `emporiosolidale`.`patrpass`.`Valore` AS `Valore`, date_format(`emporiosolidale`.`patrpass`.`Scadenza`,'%d/%m/%Y') AS `Scadenza`, `emporiosolidale`.`patrpass`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`patrpass` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionirichiedente`
--
DROP TABLE IF EXISTS `informazionirichiedente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionirichiedente`  AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`LuogoNascita` AS `LuogoNascita`, date_format(`R`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `R`.`Cittadinanza` AS `Cittadinanza`, `R`.`InItaliaDallAnno` AS `InItaliaDallAnno`, `R`.`Residenza` AS `Residenza`, `R`.`Via` AS `Via`, `R`.`TipoAlloggio` AS `TipoAlloggio`, `R`.`DocumentoIdent` AS `DocumentoIdent`, `R`.`DocumentoImmig` AS `DocumentoImmig`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `R`.`ServiziSociali` AS `ServiziSociali`, `R`.`ComeHaConosciutoEmporio` AS `ComeHaConosciutoEmporio`, `R`.`nMembri` AS `nMembri`, `R`.`ISEE` AS `ISEE`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale` FROM (((`emporiosolidale`.`richiedente` `R` left join `emporiosolidale`.`tesserarilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `emporiosolidale`.`tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `emporiosolidale`.`punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazionisss`
--
DROP TABLE IF EXISTS `informazionisss`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazionisss`  AS SELECT `emporiosolidale`.`situazionesociosanitaria`.`N` AS `N`, `emporiosolidale`.`situazionesociosanitaria`.`Evento` AS `Evento`, `emporiosolidale`.`situazionesociosanitaria`.`Anno` AS `Anno`, `emporiosolidale`.`situazionesociosanitaria`.`DocAll` AS `DocAll`, `emporiosolidale`.`situazionesociosanitaria`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`situazionesociosanitaria` ;

-- --------------------------------------------------------

--
-- Struttura per vista `informazioniuscite`
--
DROP TABLE IF EXISTS `informazioniuscite`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`informazioniuscite`  AS SELECT `emporiosolidale`.`uscite`.`N` AS `N`, `emporiosolidale`.`uscite`.`Elemento` AS `Elemento`, `emporiosolidale`.`uscite`.`Valore` AS `Valore`, date_format(`emporiosolidale`.`uscite`.`Scadenza`,'%d/%m/%Y') AS `Scadenza`, `emporiosolidale`.`uscite`.`CodFamiglia` AS `CodFamiglia` FROM `emporiosolidale`.`uscite` ;

-- --------------------------------------------------------

--
-- Struttura per vista `pass`
--
DROP TABLE IF EXISTS `pass`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`pass`  AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`emporiosolidale`.`richiedente` `R` left join `emporiosolidale`.`tesserarilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `emporiosolidale`.`tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `emporiosolidale`.`punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC ;

-- --------------------------------------------------------

--
-- Struttura per vista `storicotes`
--
DROP TABLE IF EXISTS `storicotes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `emporiosolidale`.`storicotes`  AS SELECT `ST`.`OrdineCronologico` AS `OrdineCronologico`, `ST`.`CodiceTessera` AS `CodiceTessera`, `ST`.`CodFamiglia` AS `CodFamiglia`, `R`.`Nome` AS `Nome`, `R`.`Cognome` AS `Cognome`, date_format(`ST`.`DataRilascio`,'%d/%m/%Y') AS `DataRilascio`, date_format(`ST`.`DataRitiro`,'%d/%m/%Y') AS `DataRitiro` FROM (`emporiosolidale`.`storicotessere` `ST` join `emporiosolidale`.`richiedente` `R` on(`ST`.`CodFamiglia` = `R`.`Cod_Fiscale`)) ORDER BY `ST`.`OrdineCronologico` DESC ;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Administrator`
--
ALTER TABLE `Administrator`
  ADD PRIMARY KEY (`Username`);

--
-- Indici per le tabelle `Competenze`
--
ALTER TABLE `Competenze`
  ADD PRIMARY KEY (`Nome`);

--
-- Indici per le tabelle `DispCollaborative`
--
ALTER TABLE `DispCollaborative`
  ADD PRIMARY KEY (`CodFamiglia`);

--
-- Indici per le tabelle `Entrate`
--
ALTER TABLE `Entrate`
  ADD PRIMARY KEY (`N`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `IndicatoriISEE`
--
ALTER TABLE `IndicatoriISEE`
  ADD PRIMARY KEY (`N`);

--
-- Indici per le tabelle `Membri`
--
ALTER TABLE `Membri`
  ADD PRIMARY KEY (`nMembro`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `PatrAtt`
--
ALTER TABLE `PatrAtt`
  ADD PRIMARY KEY (`N`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `PatrPass`
--
ALTER TABLE `PatrPass`
  ADD PRIMARY KEY (`N`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `Punteggio`
--
ALTER TABLE `Punteggio`
  ADD PRIMARY KEY (`CodFamiglia`);

--
-- Indici per le tabelle `PunteggioMensile`
--
ALTER TABLE `PunteggioMensile`
  ADD PRIMARY KEY (`N`);

--
-- Indici per le tabelle `Richiedente`
--
ALTER TABLE `Richiedente`
  ADD PRIMARY KEY (`Cod_Fiscale`);

--
-- Indici per le tabelle `SituazioneSocioSanitaria`
--
ALTER TABLE `SituazioneSocioSanitaria`
  ADD PRIMARY KEY (`N`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `SkillsMembri`
--
ALTER TABLE `SkillsMembri`
  ADD PRIMARY KEY (`nMembro`,`CodFamiglia`,`Nome`),
  ADD KEY `CodFamiglia` (`CodFamiglia`),
  ADD KEY `Nome` (`Nome`);

--
-- Indici per le tabelle `Stallo`
--
ALTER TABLE `Stallo`
  ADD PRIMARY KEY (`nMesiStallo`);

--
-- Indici per le tabelle `StoricoTessere`
--
ALTER TABLE `StoricoTessere`
  ADD PRIMARY KEY (`OrdineCronologico`,`CodiceTessera`,`CodFamiglia`),
  ADD KEY `CodiceTessera` (`CodiceTessera`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `Tessera`
--
ALTER TABLE `Tessera`
  ADD PRIMARY KEY (`Codice`);

--
-- Indici per le tabelle `TesseraRilasciata`
--
ALTER TABLE `TesseraRilasciata`
  ADD PRIMARY KEY (`CodiceTessera`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- Indici per le tabelle `Uscite`
--
ALTER TABLE `Uscite`
  ADD PRIMARY KEY (`N`,`CodFamiglia`),
  ADD KEY `CodFamiglia` (`CodFamiglia`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `IndicatoriISEE`
--
ALTER TABLE `IndicatoriISEE`
  MODIFY `N` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `PunteggioMensile`
--
ALTER TABLE `PunteggioMensile`
  MODIFY `N` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `StoricoTessere`
--
ALTER TABLE `StoricoTessere`
  MODIFY `OrdineCronologico` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `DispCollaborative`
--
ALTER TABLE `DispCollaborative`
  ADD CONSTRAINT `dispcollaborative_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Entrate`
--
ALTER TABLE `Entrate`
  ADD CONSTRAINT `entrate_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Membri`
--
ALTER TABLE `Membri`
  ADD CONSTRAINT `membri_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `PatrAtt`
--
ALTER TABLE `PatrAtt`
  ADD CONSTRAINT `patratt_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `PatrPass`
--
ALTER TABLE `PatrPass`
  ADD CONSTRAINT `patrpass_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Punteggio`
--
ALTER TABLE `Punteggio`
  ADD CONSTRAINT `punteggio_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `SituazioneSocioSanitaria`
--
ALTER TABLE `SituazioneSocioSanitaria`
  ADD CONSTRAINT `situazionesociosanitaria_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `SkillsMembri`
--
ALTER TABLE `SkillsMembri`
  ADD CONSTRAINT `skillsmembri_ibfk_1` FOREIGN KEY (`nMembro`) REFERENCES `Membri` (`nMembro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `skillsmembri_ibfk_2` FOREIGN KEY (`CodFamiglia`) REFERENCES `Membri` (`CodFamiglia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `skillsmembri_ibfk_3` FOREIGN KEY (`Nome`) REFERENCES `Competenze` (`Nome`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `StoricoTessere`
--
ALTER TABLE `StoricoTessere`
  ADD CONSTRAINT `storicotessere_ibfk_1` FOREIGN KEY (`CodiceTessera`) REFERENCES `Tessera` (`Codice`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `storicotessere_ibfk_2` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `TesseraRilasciata`
--
ALTER TABLE `TesseraRilasciata`
  ADD CONSTRAINT `tesserarilasciata_ibfk_1` FOREIGN KEY (`CodiceTessera`) REFERENCES `Tessera` (`Codice`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tesserarilasciata_ibfk_2` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Uscite`
--
ALTER TABLE `Uscite`
  ADD CONSTRAINT `uscite_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Password sbagliate recenti, per fermare chi prova a indovinarla
--

CREATE TABLE `TentativiAccesso` (
  `Indirizzo` varchar(45) NOT NULL,
  `Quando` int(11) NOT NULL,
  KEY `Indirizzo` (`Indirizzo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Punti della griglia di valutazione, modificabili dalla configurazione
--

CREATE TABLE `RegolePunteggio` (
  `Chiave` varchar(50) NOT NULL,
  `Punti` int(11) NOT NULL,
  PRIMARY KEY (`Chiave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `RegolePunteggio` (`Chiave`, `Punti`) VALUES
('minors_one_adult', 15),
('minors_more_adults', 10),
('minors_each_other', 4),
('unemployed_adult', 10),
('semi_employed_adult', 5),
('single_parent_unemployed', 10),
('both_parents_unemployed', 10),
('one_parent_of_two_unemployed', 5),
('other_adults', 5),
('disability_total_in_household', 10),
('disability_total_alone', 20),
('disability_severe_adult_in_household', 5),
('disability_severe_adult_alone', 10),
('disability_severe_minor', 10),
('debt_seizure', 5),
('debt_arrears', 5),
('debt_eviction', 10),
('debt_other_min', 2),
('debt_other_max', 10),
('benefit_public_housing', 10),
('benefit_income_support', 10),
('benefit_care_allowance', 10),
('benefit_other_min', 2),
('benefit_other_max', 5);

--
-- Voci della griglia valutate dall'operatore per ogni domanda
--

CREATE TABLE `ValutazioneGriglia` (
  `CodFamiglia` char(16) NOT NULL,
  `InvaliditaTotaleAdulti` int(11) NOT NULL DEFAULT 0,
  `InvaliditaGraveAdulti` int(11) NOT NULL DEFAULT 0,
  `InvaliditaGraveMinori` int(11) NOT NULL DEFAULT 0,
  `AltriDebiti` int(11) NOT NULL DEFAULT 0,
  `AltriContributi` int(11) NOT NULL DEFAULT 0,
  `NoteCommissione` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`CodFamiglia`),
  CONSTRAINT `valutazionegriglia_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
