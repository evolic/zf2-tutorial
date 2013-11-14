
--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: cuisines; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO cuisines VALUES (1, 'Polish');
INSERT INTO cuisines VALUES (2, 'Mexican');
INSERT INTO cuisines VALUES (3, 'Italian');


--
-- Name: cuisines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('cuisines_id_seq', 3, true);


--
-- Data for Name: desserts; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO desserts VALUES (1, 'Polish donuts', 6);
INSERT INTO desserts VALUES (2, 'Cheesecake', 8);
INSERT INTO desserts VALUES (3, 'Poppy-seed cake', 7);
INSERT INTO desserts VALUES (4, 'Gingerbread', 7);


--
-- Name: desserts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('desserts_id_seq', 4, true);


--
-- Data for Name: drinks; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO drinks VALUES (1, 'Compote', 6);
INSERT INTO drinks VALUES (2, 'Orangeade', 5);
INSERT INTO drinks VALUES (3, 'Beer', 8);
INSERT INTO drinks VALUES (4, 'Mead', 7);
INSERT INTO drinks VALUES (5, 'Tea', 4);


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO orders VALUES (1, 8, '2013-11-11 11:04:41');
INSERT INTO orders VALUES (2, 15, '2013-11-11 11:14:44');
INSERT INTO orders VALUES (3, 16, '2013-11-11 11:43:47');
INSERT INTO orders VALUES (4, 18, '2013-11-11 12:06:50');
INSERT INTO orders VALUES (5, 17, '2013-11-11 12:11:53');
INSERT INTO orders VALUES (6, 6, '2013-11-11 12:19:56');
INSERT INTO orders VALUES (7, 12, '2013-11-11 12:25:00');
INSERT INTO orders VALUES (8, 5, '2013-11-11 12:35:04');
INSERT INTO orders VALUES (9, 4, '2013-11-11 12:42:01');
INSERT INTO orders VALUES (10, 20, '2013-11-11 13:28:46');
INSERT INTO orders VALUES (11, 17, '2013-11-11 13:59:35');
INSERT INTO orders VALUES (13, 4, '2013-11-14 01:03:12');
INSERT INTO orders VALUES (14, 5, '2013-11-14 01:05:03');
INSERT INTO orders VALUES (16, 8, '2013-11-14 01:41:13');
INSERT INTO orders VALUES (18, 5, '2013-11-14 01:43:07');
INSERT INTO orders VALUES (23, 16, '2013-11-14 01:53:49');
INSERT INTO orders VALUES (24, 16, '2013-11-14 01:54:05');
INSERT INTO orders VALUES (25, 17, '2013-11-14 02:05:31');
INSERT INTO orders VALUES (26, 8, '2013-11-14 02:06:03');
INSERT INTO orders VALUES (27, 18, '2013-11-14 02:13:40');


--
-- Data for Name: drink2order; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO drink2order VALUES (1, 6, false, false);
INSERT INTO drink2order VALUES (2, 8, false, true);
INSERT INTO drink2order VALUES (5, 9, true, false);
INSERT INTO drink2order VALUES (5, 13, true, false);
INSERT INTO drink2order VALUES (2, 14, false, true);
INSERT INTO drink2order VALUES (3, 16, false, false);
INSERT INTO drink2order VALUES (2, 18, false, false);
INSERT INTO drink2order VALUES (3, 26, false, false);


--
-- Name: drinks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('drinks_id_seq', 5, true);


--
-- Data for Name: meals; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO meals VALUES (1, 1, 'Pierogi', 8);
INSERT INTO meals VALUES (2, 1, 'Polish hunter''s stew', 9);
INSERT INTO meals VALUES (3, 1, 'Polish cabbage rolls in tomato sauce', 9);
INSERT INTO meals VALUES (4, 2, 'Mole poblano', 10);
INSERT INTO meals VALUES (5, 2, 'Cemita with milanesa', 9);
INSERT INTO meals VALUES (6, 2, 'Molotes', 10);
INSERT INTO meals VALUES (7, 3, 'Chicken Saltimbocca', 12);
INSERT INTO meals VALUES (8, 3, 'Slow Cooked Pork Shanks With Gremolata (Stinco)', 14);
INSERT INTO meals VALUES (9, 3, 'Cheesy Cauliflower Puree', 13);
INSERT INTO meals VALUES (10, 3, 'Farfalle Pasta With Broccoli Pesto', 12);


--
-- Data for Name: lunches; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO lunches VALUES (1, 1, NULL);
INSERT INTO lunches VALUES (2, 2, 1);
INSERT INTO lunches VALUES (3, 3, 3);
INSERT INTO lunches VALUES (4, 4, 2);
INSERT INTO lunches VALUES (5, 6, 4);
INSERT INTO lunches VALUES (6, 7, NULL);
INSERT INTO lunches VALUES (7, 9, 3);
INSERT INTO lunches VALUES (8, 2, 2);
INSERT INTO lunches VALUES (9, 1, 2);
INSERT INTO lunches VALUES (10, 1, 2);
INSERT INTO lunches VALUES (11, 4, 4);
INSERT INTO lunches VALUES (12, 7, 1);


--
-- Data for Name: lunch2order; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO lunch2order VALUES (1, 1);
INSERT INTO lunch2order VALUES (2, 2);
INSERT INTO lunch2order VALUES (3, 3);
INSERT INTO lunch2order VALUES (4, 4);
INSERT INTO lunch2order VALUES (5, 5);
INSERT INTO lunch2order VALUES (6, 7);
INSERT INTO lunch2order VALUES (7, 10);
INSERT INTO lunch2order VALUES (8, 11);
INSERT INTO lunch2order VALUES (9, 23);
INSERT INTO lunch2order VALUES (10, 24);
INSERT INTO lunch2order VALUES (11, 25);
INSERT INTO lunch2order VALUES (12, 27);


--
-- Name: lunches_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('lunches_id_seq', 12, true);


--
-- Name: meals_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('meals_id_seq', 10, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('orders_id_seq', 27, true);


--
-- PostgreSQL database dump complete
--


