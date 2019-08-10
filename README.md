# Income-Tax-System
A web portal to calculate income tax of PICT employees. Portal currently being used by college Accounts section.

## Installation
1) Install XAMPP on server machine.
2) Download this repo as zip and copy extracted folder to xampp/htdocs.
3) Start the Apache web server and MySQL server.
4) Open Phpmyadmin in browser and import the Income_Tax.sql file.
5) Now the web portal can be accessed in the web browser from host machine as well as remote machine.
5) For host machine, type "http://localhost/Income_Tax/login.html".
7) For remote machine on same network as that of server machine, type "http://ip_addr_of_server/Income_Tax/login.html".
8) Credentials for login present in the relation Users.

## Functionalities
### User
1) Enter/Edit/Remove declaration amount of any employee.
2) Validate declaration amount of any employee.
3) View taxable income of any employee.
4) View income tax of any employee.

### Admin
1) Add a new declaration field.
2) Remove an existing declaration field.
3) Modify exemption limits of declaration fields.
4) Modify tax slabs.
5) Update gross salary of employees.
6) Reset entire database. To be performed at the beginning of every financial year.
