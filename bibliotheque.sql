CREATE DATABASE bibliotheque;

   USE bibliotheque;

   CREATE TABLE utilisateurs (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nom VARCHAR(50),
       prenom VARCHAR(50),
       niveau_etude VARCHAR(50),
       numero_whatsapp VARCHAR(15),
       code_acces VARCHAR(8)
   );
