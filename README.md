## Kurulum
- Proje dizininde `composer install` calistirilir
- Database ayarlari icin `.env` dosyasinda `DATABASE_URL=mysql://KULLANICIADI:SIFRE@127.0.0.1:3306/DATABASENAME`
- Database yok ise `php bin/console doctrine:database:create` komutu ile database olustur.
- `php bin/console doctrine:schema:update --force` ile tabloları oluştur.
- `php bin/console custom:seed-products` komutu ile ürünleri ekle
- `php bin/phpunit` testleri çalıştırır.

## Açıklama

Ön sipariş süresi bir günü geçen verilerin durumunu **auto-rejected** yapmak için crona eklenmesi gereken komut:
```
0 * * * * /path/to/php /path/to/bin/console custom:update-status
```
