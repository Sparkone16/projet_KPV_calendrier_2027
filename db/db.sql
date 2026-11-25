CREATE IF NOT EXISTS DATABASE  WEBL2_PROJET_2026_27;

CREATE IF NOT EXISTS TABLE G_KPV_calendrier
(
    id_calendrier INT PRIMARY KEY,
    nom_calendrier VARCHAR(255) NOT NULL,
    description_calendrier TEXT
);

CREATE IF NOT EXISTS TABLE G_KPV_evenement
(
    id_evenement INT PRIMARY KEY,
    nom_evenement VARCHAR(255) NOT NULL,
    date_evenement DATE NOT NULL,
    heure_evenement TIME NOT NULL,
    description_evenement TEXT,
    id_calendrier INT,
    FOREIGN KEY (id_calendrier) REFERENCES G_KPV_calendrier(id_calendrier)
)