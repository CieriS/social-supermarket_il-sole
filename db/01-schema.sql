-- Tables of the application, with their keys and constraints. No views: some
-- shared hosts do not allow creating them, so the queries that used to read
-- views select from the tables themselves (src/Views.php).
-- Safe to run again: existing tables are left as they are. The rows the
-- application needs to work are in 02-configuration.sql.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `Administrator` (
  `Username` varchar(15) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Competenze` (
  `Nome` varchar(30) NOT NULL,
  `Descrizione` tinytext,
  `Presso` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`Nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Richiedente` (
  `Cod_Fiscale` char(16) NOT NULL,
  `Cognome` varchar(30) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `LuogoNascita` varchar(30) NOT NULL,
  `DataNascita` date NOT NULL,
  `Cittadinanza` varchar(15) NOT NULL,
  `InItaliaDallAnno` year DEFAULT NULL,
  `Residenza` varchar(30) NOT NULL,
  `Via` varchar(30) NOT NULL,
  `TipoAlloggio` varchar(30) NOT NULL,
  `DocumentoIdent` char(9) DEFAULT NULL,
  `DocumentoImmig` char(10) DEFAULT NULL,
  `Telefono` varchar(15) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `ServiziSociali` varchar(30) NOT NULL,
  `ComeHaConosciutoEmporio` varchar(30) DEFAULT NULL,
  `nMembri` int NOT NULL,
  `DataOraRitiroTessera` date DEFAULT NULL,
  `DataOraStalloTessera` date DEFAULT NULL,
  `ISEE` int NOT NULL,
  PRIMARY KEY (`Cod_Fiscale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Tessera` (
  `Codice` char(12) NOT NULL,
  PRIMARY KEY (`Codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `IndicatoriISEE` (
  `N` int NOT NULL AUTO_INCREMENT,
  `ISEEmin` int NOT NULL,
  `ISEEmax` int NOT NULL,
  `Punteggio` int NOT NULL,
  PRIMARY KEY (`N`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PunteggioMensile` (
  `N` int NOT NULL AUTO_INCREMENT,
  `nComponenti` int NOT NULL,
  `ValoreEuro` decimal(5,2) NOT NULL,
  `ValorePunti` int NOT NULL,
  PRIMARY KEY (`N`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Stallo` (
  `nMesiStallo` int NOT NULL,
  PRIMARY KEY (`nMesiStallo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `RegolePunteggio` (
  `Chiave` varchar(50) NOT NULL,
  `Punti` int NOT NULL,
  PRIMARY KEY (`Chiave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `StatoDemo` (
  `UltimoRipristino` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `TentativiAccesso` (
  `Indirizzo` varchar(45) NOT NULL,
  `Quando` int NOT NULL,
  KEY `Indirizzo` (`Indirizzo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `DispCollaborative` (
  `CodFamiglia` char(16) NOT NULL,
  `AmbitoFormativo` tinytext,
  `AmbitoLavorativo` tinytext,
  `AmbitoSociale` tinytext,
  PRIMARY KEY (`CodFamiglia`),
  CONSTRAINT `dispcollaborative_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Entrate` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL,
  PRIMARY KEY (`N`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `entrate_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Membri` (
  `nMembro` int NOT NULL,
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
  `CodFamiglia` char(16) NOT NULL,
  PRIMARY KEY (`nMembro`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `membri_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PatrAtt` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`N`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `patratt_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PatrPass` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL,
  PRIMARY KEY (`N`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `patrpass_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Punteggio` (
  `CodFamiglia` char(16) NOT NULL,
  `PunteggioISEE` int DEFAULT '0',
  `PunteggioPresenzaMinori` int DEFAULT '0',
  `PunteggioDisoccupazione` int DEFAULT '0',
  `PunteggioInvalidi` int DEFAULT '0',
  `PunteggioSituazioneDebitoria` int DEFAULT '0',
  `PunteggioBenefit` int DEFAULT '0',
  `PunteggioTotale` int DEFAULT '0',
  PRIMARY KEY (`CodFamiglia`),
  CONSTRAINT `punteggio_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `SituazioneSocioSanitaria` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int NOT NULL,
  `Evento` varchar(60) NOT NULL,
  `Anno` year DEFAULT NULL,
  `DocAll` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`N`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `situazionesociosanitaria_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `SkillsMembri` (
  `nMembro` int NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  PRIMARY KEY (`nMembro`,`CodFamiglia`,`Nome`),
  KEY `Nome` (`Nome`),
  CONSTRAINT `skillsmembri_ibfk_1` FOREIGN KEY (`nMembro`, `CodFamiglia`) REFERENCES `Membri` (`nMembro`, `CodFamiglia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `skillsmembri_ibfk_3` FOREIGN KEY (`Nome`) REFERENCES `Competenze` (`Nome`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `StoricoTessere` (
  `OrdineCronologico` int NOT NULL AUTO_INCREMENT,
  `CodiceTessera` char(12) NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `DataRilascio` date NOT NULL,
  `DataRitiro` date DEFAULT NULL,
  PRIMARY KEY (`OrdineCronologico`,`CodiceTessera`,`CodFamiglia`),
  KEY `CodiceTessera` (`CodiceTessera`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `storicotessere_ibfk_1` FOREIGN KEY (`CodiceTessera`) REFERENCES `Tessera` (`Codice`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `storicotessere_ibfk_2` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `TesseraRilasciata` (
  `CodiceTessera` char(12) NOT NULL,
  `CodFamiglia` char(16) NOT NULL,
  `DataOraRilascioTessera` date DEFAULT NULL,
  `DataOraScadenzaTessera` date DEFAULT NULL,
  PRIMARY KEY (`CodiceTessera`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `tesserarilasciata_ibfk_1` FOREIGN KEY (`CodiceTessera`) REFERENCES `Tessera` (`Codice`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tesserarilasciata_ibfk_2` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Uscite` (
  `CodFamiglia` char(16) NOT NULL,
  `N` int NOT NULL,
  `Elemento` varchar(60) NOT NULL,
  `Valore` decimal(8,2) DEFAULT NULL,
  `Scadenza` date DEFAULT NULL,
  PRIMARY KEY (`N`,`CodFamiglia`),
  KEY `CodFamiglia` (`CodFamiglia`),
  CONSTRAINT `uscite_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ValutazioneGriglia` (
  `CodFamiglia` char(16) NOT NULL,
  `InvaliditaTotaleAdulti` int NOT NULL DEFAULT '0',
  `InvaliditaGraveAdulti` int NOT NULL DEFAULT '0',
  `InvaliditaGraveMinori` int NOT NULL DEFAULT '0',
  `AltriDebiti` int NOT NULL DEFAULT '0',
  `AltriContributi` int NOT NULL DEFAULT '0',
  `NoteCommissione` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`CodFamiglia`),
  CONSTRAINT `valutazionegriglia_ibfk_1` FOREIGN KEY (`CodFamiglia`) REFERENCES `Richiedente` (`Cod_Fiscale`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
