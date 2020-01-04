# Upgrade Guide

## Upgrading from 1.* to 2.0.0

### Namespace Change
The namespace for the ``` ExchangeRate ``` class was originally ``` AshAllenDesign\LaravelExchangeRates ```. This has
now been updated to ``` AshAllenDesign\LaravelExchangeRates\Classes ``` to be consistent with other classes in the
library. Anywhere that this class has been used, you will need to update the import. 

The snippets below show an example of how the namespaces need updating:

Change from this:
```php
<?php

    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    ...

```

to this:
```php
<?php

    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
    ...

```