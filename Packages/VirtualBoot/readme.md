# Installation

Run the following queries:

```
CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `priorities` (
  `priority_id` int(11) NOT NULL,
  `component_type` varchar(100) NOT NULL,
  `component_name` varchar(100) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE','PATCH') NOT NULL,
  `route` varchar(100) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `middleware` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```