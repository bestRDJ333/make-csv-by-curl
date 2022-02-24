# 介紹

## 安裝

- 請先依照 [wp-proxy-companion](https://github.com/bestRDJ333/wp-proxy-companion)安裝docker與docker-compose

- 依照[wp-proxy-sites](https://github.com/bestRDJ333/wp-proxy-sites)建立程式運行phpweb容器

macOS 前往/private/etc/hosts

設定與 .yml檔中 VIRTUAL_HOST 一組對應的網址

例如

.yml

```
VIRTUAL_HOST: phpweb.test
```

hosts

```
127.0.0.1       phpweb.test
```



運行容器後在瀏覽器網址列上打入剛剛設定的對應網址

就會看到網站畫面

顯示網站的資源資料夾在 .yml中的 volumes設定

以此為例就是對應在www資料夾

```
./sites/phpweb/www:/var/www/html/
```



