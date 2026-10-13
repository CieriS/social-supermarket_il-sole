-- Schema of the application: tables first, then keys and constraints, then
-- the views the pages read. The rows it needs to work are in 02-configuration.sql.

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


-- --------------------------------------------------------


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


-- --------------------------------------------------------


-- --------------------------------------------------------


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


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


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


-- --------------------------------------------------------


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
  `ServiziSociali` varchar(30) NOT NULL,
  `ComeHaConosciutoEmporio` varchar(30) DEFAULT NULL,
  `nMembri` int(2) NOT NULL,
  `DataOraRitiroTessera` date DEFAULT NULL,
  `DataOraStalloTessera` date DEFAULT NULL,
  `ISEE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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


-- --------------------------------------------------------


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


-- --------------------------------------------------------


CREATE VIEW `CompetenzeView` AS SELECT `Membri`.`nMembro` AS `nMembro`, `Membri`.`NomeCognome` AS `NomeCognome`, `Membri`.`LuogoNascita` AS `LuogoNascita`, date_format(`Membri`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `Membri`.`Parentela` AS `Parentela`, `Membri`.`Occupazione` AS `Occupazione`, `Membri`.`TitoloDiStudio` AS `TitoloDiStudio`, `Membri`.`Competenze` AS `Competenze`, `Membri`.`ConoscenzaLingua` AS `ConoscenzaLingua`, `Membri`.`Patente` AS `Patente`, `Membri`.`AttualeAtt` AS `AttualeAtt`, `Membri`.`Presso` AS `Presso`, `Membri`.`Termine` AS `Termine`, `Membri`.`SenzaLavoroDa` AS `SenzaLavoroDa`, `Membri`.`CodFamiglia` AS `CodFamiglia` FROM `Membri` ORDER BY `Membri`.`CodFamiglia` ASC, `Membri`.`nMembro` ASC;

-- --------------------------------------------------------


CREATE VIEW `Confirmation` AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%Y/%m/%d') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%Y/%m/%d') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%Y/%m/%d') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%Y/%m/%d') AS `DataOraStalloTessera` FROM (((`Richiedente` `R` left join `TesseraRilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `Tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `Punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC;

-- --------------------------------------------------------


CREATE VIEW `Export` AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`Cittadinanza` AS `Cittadinanza`, `R`.`LuogoNascita` AS `LuogoNascita`, `R`.`DataNascita` AS `DataNascita`, `R`.`TipoAlloggio` AS `TipoAlloggio`, `R`.`Residenza` AS `Residenza`, `R`.`Via` AS `Via`, `R`.`nMembri` AS `nMembri`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`Richiedente` `R` left join `TesseraRilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `Tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `Punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC;

-- --------------------------------------------------------


CREATE VIEW `Graduatoria` AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`Richiedente` `R` left join `TesseraRilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `Tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `Punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC;

-- --------------------------------------------------------


CREATE VIEW `InformazioniDispColl` AS SELECT `DispCollaborative`.`AmbitoFormativo` AS `AmbitoFormativo`, `DispCollaborative`.`AmbitoLavorativo` AS `AmbitoLavorativo`, `DispCollaborative`.`AmbitoSociale` AS `AmbitoSociale`, `DispCollaborative`.`CodFamiglia` AS `CodFamiglia` FROM `DispCollaborative`;

-- --------------------------------------------------------


-- --------------------------------------------------------


CREATE VIEW `InformazioniMembri` AS SELECT `Membri`.`nMembro` AS `nMembro`, `Membri`.`NomeCognome` AS `NomeCognome`, `Membri`.`LuogoNascita` AS `LuogoNascita`, date_format(`Membri`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `Membri`.`Parentela` AS `Parentela`, `Membri`.`Occupazione` AS `Occupazione`, `Membri`.`TitoloDiStudio` AS `TitoloDiStudio`, `Membri`.`Competenze` AS `Competenze`, `Membri`.`ConoscenzaLingua` AS `ConoscenzaLingua`, `Membri`.`Patente` AS `Patente`, `Membri`.`AttualeAtt` AS `AttualeAtt`, `Membri`.`Presso` AS `Presso`, `Membri`.`Termine` AS `Termine`, `Membri`.`SenzaLavoroDa` AS `SenzaLavoroDa`, `Membri`.`CodFamiglia` AS `CodFamiglia` FROM `Membri`;

-- --------------------------------------------------------


-- --------------------------------------------------------


-- --------------------------------------------------------


CREATE VIEW `InformazioniRichiedente` AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`LuogoNascita` AS `LuogoNascita`, date_format(`R`.`DataNascita`,'%d/%m/%Y') AS `DataNascita`, `R`.`Cittadinanza` AS `Cittadinanza`, `R`.`InItaliaDallAnno` AS `InItaliaDallAnno`, `R`.`Residenza` AS `Residenza`, `R`.`Via` AS `Via`, `R`.`TipoAlloggio` AS `TipoAlloggio`, `R`.`DocumentoIdent` AS `DocumentoIdent`, `R`.`DocumentoImmig` AS `DocumentoImmig`, `R`.`Telefono` AS `Telefono`, `R`.`Mail` AS `Mail`, `R`.`ServiziSociali` AS `ServiziSociali`, `R`.`ComeHaConosciutoEmporio` AS `ComeHaConosciutoEmporio`, `R`.`nMembri` AS `nMembri`, `R`.`ISEE` AS `ISEE`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale` FROM (((`Richiedente` `R` left join `TesseraRilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `Tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `Punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC;

-- --------------------------------------------------------


CREATE VIEW `InformazioniSSS` AS SELECT `SituazioneSocioSanitaria`.`N` AS `N`, `SituazioneSocioSanitaria`.`Evento` AS `Evento`, `SituazioneSocioSanitaria`.`Anno` AS `Anno`, `SituazioneSocioSanitaria`.`DocAll` AS `DocAll`, `SituazioneSocioSanitaria`.`CodFamiglia` AS `CodFamiglia` FROM `SituazioneSocioSanitaria`;

-- --------------------------------------------------------


-- --------------------------------------------------------


CREATE VIEW `Pass` AS SELECT `R`.`Cod_Fiscale` AS `Cod_Fiscale`, `R`.`Cognome` AS `Cognome`, `R`.`Nome` AS `Nome`, `R`.`nMembri` AS `nMembri`, `P`.`PunteggioISEE` AS `PunteggioISEE`, `P`.`PunteggioPresenzaMinori` AS `PunteggioPresenzaMinori`, `P`.`PunteggioDisoccupazione` AS `PunteggioDisoccupazione`, `P`.`PunteggioInvalidi` AS `PunteggioInvalidi`, `P`.`PunteggioSituazioneDebitoria` AS `PunteggioSituazioneDebitoria`, `P`.`PunteggioBenefit` AS `PunteggioBenefit`, `P`.`PunteggioTotale` AS `PunteggioTotale`, `TR`.`CodiceTessera` AS `CodiceTessera`, date_format(`TR`.`DataOraRilascioTessera`,'%d/%m/%Y') AS `DataOraRilascioTessera`, date_format(`TR`.`DataOraScadenzaTessera`,'%d/%m/%Y') AS `DataOraScadenzaTessera`, date_format(`R`.`DataOraRitiroTessera`,'%d/%m/%Y') AS `DataOraRitiroTessera`, date_format(`R`.`DataOraStalloTessera`,'%d/%m/%Y') AS `DataOraStalloTessera` FROM (((`Richiedente` `R` left join `TesseraRilasciata` `TR` on(`R`.`Cod_Fiscale` = `TR`.`CodFamiglia`)) left join `Tessera` `T` on(`TR`.`CodiceTessera` = `T`.`Codice`)) join `Punteggio` `P` on(`R`.`Cod_Fiscale` = `P`.`CodFamiglia`)) ORDER BY `P`.`PunteggioTotale` DESC;

-- --------------------------------------------------------


CREATE VIEW `StoricoTes` AS SELECT `ST`.`OrdineCronologico` AS `OrdineCronologico`, `ST`.`CodiceTessera` AS `CodiceTessera`, `ST`.`CodFamiglia` AS `CodFamiglia`, `R`.`Nome` AS `Nome`, `R`.`Cognome` AS `Cognome`, date_format(`ST`.`DataRilascio`,'%d/%m/%Y') AS `DataRilascio`, date_format(`ST`.`DataRitiro`,'%d/%m/%Y') AS `DataRitiro` FROM (`StoricoTessere` `ST` join `Richiedente` `R` on(`ST`.`CodFamiglia` = `R`.`Cod_Fiscale`)) ORDER BY `ST`.`OrdineCronologico` DESC;

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
-- Momento dell'ultimo ripristino dei dati dimostrativi (solo in modalita demo)
--

CREATE TABLE `StatoDemo` (
  `UltimoRipristino` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
