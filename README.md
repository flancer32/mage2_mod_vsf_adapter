# mage2_mod_vsf_adapter
Magento 2 module to use Magento apps with VueStorefront.

**Attention: this module is not production ready.**


## Functionality

* Full replication for catalog data (categories, products & products attributes) been started from CLI & adminhtml.
* Setup default store if missed for REST requests from VueStorefront API.


## Start replication from CLI

```shell script
$ ./bin/magento fl32:vsf:replicate:attr -i vsf_store3_ -s 3
$ ./bin/magento fl32:vsf:replicate:category -i vsf_store3_ -s 3
$ ./bin/magento fl32:vsf:replicate:product -i vsf_store3_ -s 3
```
