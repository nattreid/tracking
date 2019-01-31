# Tracking pro Nette Framework

## Nastaveni
**config.neon**
```neon
extensions:
    tracking: NAttreid\Tracking\DI\TrackingExtension
```

Možné nastavení
```neon
tracking:
    trackUrl: 'track'
    clickUrl: 'clickTrack'
    minTimeBetweenVisits: 30 # v minutach
    onlineTime: 3 # v minutach
    anonymizeIp: false
    trackBot: false # ukládat i roboty?
```

A přidat do orm model trackingu. V příkladu je extension orm pod nazvem **orm**
```neon
orm:
    add:
        - NAttreid\Tracking\Model\Orm
```

Použítí
```php
/** @var \NAttreid\Tracking\Tracking @inject */
public $tracking;
```

## Klient
```html
<script async type="text/javascript" src="/js/tracking/nTracker.min.js"></script>
```

### Trackování kliků
Do html elementu přidate atribut **data-nctr="nazev_ktery_chcete_logovat"**. Pro doplňující informace slouží atribut **data-ncval="hodnota"** a pokud chcete hodnoty slučovat nebo průměrovat, tak k tomu slouží atributy **data-ncavg="hodnota"** a **data-ncsum="hodnota"**
```html
<a href="/test" data-nctr="testingPage" data-ncval="test" data-ncavg="5" data-ncsum="10">Test</a>
```