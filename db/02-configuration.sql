-- Configuration the application needs to work: ISEE brackets, monthly points,
-- waiting period, points of the evaluation grid. The configuration page edits
-- these rows; loading this file again brings back the values of the grid.

SET NAMES utf8mb4;

INSERT INTO `IndicatoriISEE` (`N`, `ISEEmin`, `ISEEmax`, `Punteggio`) VALUES
(1, 0, 1500, 100),
(2, 1501, 3000, 80),
(3, 3001, 4500, 60),
(4, 4501, 6000, 40),
(5, 6001, 7500, 20);

INSERT INTO `PunteggioMensile` (`N`, `nComponenti`, `ValoreEuro`, `ValorePunti`) VALUES
(1, 1, '150.00', 300),
(2, 2, '200.00', 400),
(3, 3, '250.00', 500),
(4, 4, '300.00', 600);

INSERT INTO `Stallo` (`nMesiStallo`) VALUES
(12);

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
