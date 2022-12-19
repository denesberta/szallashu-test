SELECT
    CASE
        WHEN activity = 'Building Industry' THEN companyName
        ELSE ''
        END AS "Building Industry",
    CASE
        WHEN activity = 'Food' THEN companyName
        ELSE ''
        END AS "Food",
    CASE
        WHEN activity = 'IT' THEN companyName
        ELSE ''
        END AS "IT",
    CASE
        WHEN activity = 'Car' THEN companyName
        ELSE ''
        END AS "Car",
    CASE
        WHEN activity = 'Growing Plants' THEN companyName
        ELSE ''
        END AS "Growing Plants"
FROM companyRaw;

-- MySql sajnos nem tud pivot táblát csinálni, így a fenti query-t kell használni.
