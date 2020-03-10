# mage2_mod_vsf_adapter
Magento 2 module to use Magento apps with VueStorefront.

**Attention: this module is used for one project only and is not generic. **


## Functionality

* Full replication for catalog data (categories, products & products attributes) been started from CLI & adminhtml.
* Inventory data replication for items in Elasticsearch (product prices & qty) been started from CLI & adminhtml.



## Start replication from CLI

```shell script
$ ./bin/magento fl32:vsf:replicate:catalog -i vsf_store3_ -s 3
$ ./bin/magento fl32:vsf:replicate:inventory -i vsf_store3_ -s 3
```


```

```
