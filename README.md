# SocialNetwork

Readme SocialNetwork :

La compréhension de SQL - Les bases !

1. Sélection de toutes les colonnes d'un tableau

SELECT *
FROM employees;

Ce mot-clé est suivi d'un astérisque (*), qui signifie "toutes les colonnes de la table".

Pour spécifier la table, utilisez la clause FROM et écrivez le nom de la table à la suite.
2. Sélection d'une colonne d'une table

SELECT first_name
FROM employees;

On ajoute WHERE salary > 3800;
Nous devons maintenant afficher uniquement les employés dont le salaire est supérieur à 3 800. Pour ce faire, vous devez utiliser WHERE. Il s'agit d'une clause qui accepte des conditions et qui est utilisée pour filtrer les résultats. Elle parcourt le tableau et renvoie uniquement les données qui satisfont à la condition.

Accès aux données de deux tables à l'aide d'une jointure interne (INNER JOIN)
Ce type de requête est utilisé lorsque vous souhaitez accéder à des données provenant de deux tables ou plus
