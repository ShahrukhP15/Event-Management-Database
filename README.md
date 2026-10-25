# Event Management Database

A relational database and web application for running an events company: venues and seating, concerts, plays and conferences, ticket sales, customer loyalty tiers, sponsorships and staff assignments.

![Oracle](https://img.shields.io/badge/Oracle-SQL-F80000?logo=oracle&logoColor=white) ![PHP](https://img.shields.io/badge/PHP-OCI8-777BB4?logo=php&logoColor=white) ![Normalized](https://img.shields.io/badge/Schema-BCNF-2E8B57)

## Highlights
- **19-table schema normalized to BCNF**, designed from an ER diagram with documented functional dependencies and decompositions
- **Role-based web interface** in PHP with separate pages for event managers, customer service and HR
- **Full range of SQL operations:** inserts, updates and deletes, joins, aggregation with GROUP BY and HAVING, nested aggregation, and relational division
- **Integrity constraints** through primary keys, foreign keys, unique and NOT NULL constraints, and ON DELETE behaviour

## Data model
The database models how an events company operates:

- **Venues** have seats, a type, a rental price and amenities
- **Events** happen at one venue and can be a **concert**, **play** or **conference**, or more than one (a musical can be both a concert and a play)
- **Customers** buy **tickets** through **transactions** and earn loyalty points that place them in a **tier**
- **Sponsors** fund one or more events
- **Staff** are assigned to events, with a role that can differ from event to event

### Normalization
Starting from the ER diagram, each relation was checked against its functional dependencies and decomposed into BCNF where needed. For example, venue pricing depends on venue type and capacity rather than on the individual venue, so it moved to its own `Rent` table. Customer tier depends only on loyalty points, so it became a separate `CustomerTier` table. The full ER diagram, functional dependencies and decomposition steps are in the [design document](2.%20ER%20Diagrams,%20Relation%20Schemas,%20FDs,%20Normalization,%20SQL%20DDL.pdf).

## Application
The home page links to three role-based views:

| Page | What it does |
|---|---|
| **Event Manager** | View, update and delete events; add sponsors and sponsorships; total sponsors per event; filter events by total sponsorship funding |
| **Customer Service** | View customers and loyalty tiers; find customers by tier or by event attended; find the customer with the most transactions |
| **HR** | View staff assignments; choose which staff fields to display; find staff assigned to every event |

### Example queries
**Relational division:** staff who have worked at every event

    SELECT DISTINCT s.staffid, s.staffname
    FROM staff s
    WHERE NOT EXISTS (
        SELECT e.eventid FROM event e
        WHERE NOT EXISTS (
            SELECT a.eventid FROM assignment a
            WHERE a.staffid = s.staffid AND a.eventid = e.eventid));

**Nested aggregation:** the customer(s) with the most transactions

    SELECT t.custid, COUNT(*) AS count
    FROM transaction t
    GROUP BY t.custid
    HAVING COUNT(*) >= ALL (SELECT COUNT(*) FROM transaction t2 GROUP BY t2.custid);

Every query, with its location in the code and before-and-after screenshots, is documented in the [queries document](3.%20SQL%20Queries%20and%20Screenshots%20.pdf).

## Project structure

    ├── index.php                    home page with links to each role
    ├── event_manager.php            event, venue and sponsorship management
    ├── customerservice.php          customer, ticket and loyalty queries
    ├── hr.php                       staff and assignment queries
    ├── functions.php                shared database connection and query helpers
    ├── event_management.sql         schema (19 tables) and sample data
    ├── style.css
    ├── images/
    ├── 1. Project Description.pdf
    ├── 2. ER Diagrams, Relation Schemas, FDs, Normalization, SQL DDL.pdf
    └── 3. SQL Queries and Screenshots.pdf

The app was built for UBC's student Oracle server and runs on any Oracle database with PHP and OCI8, after loading `event_management.sql` and setting the connection details at the top of each page.

## Known limitations
Most queries insert user input directly into SQL strings, which leaves them open to SQL injection. Production code should use bound parameters; the `executeBoundSQL` helper in `functions.php` already supports this.
