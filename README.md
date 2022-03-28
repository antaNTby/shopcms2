# MapsShop (old engine)

## Stack

- Apache	2.4.6-97.el7_9.cloudlinux
- MariaDb	10.3.31-1.el7.centos
- PHP	5.4.16-48.el7.cloudlinux (mysql)
- Perl	5.16.3-299.el7_9
- Kernel	3.10.0-962.3.2.lve1.5.49.el7.x86_64

## Config
`core/confg/domains/{domain}.php`

## Deploy
- Create `core/cache/{domain}` directory.
- Must be writable directories:
  - `core/cache`; 
  - `core/cache/{domain}`; 
  - `sxd/backup`; 