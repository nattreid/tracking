# Tracking pro Nette Framework

## Klient
```html
<script async type="text/javascript" src="/js/tracking/nTracker.min.js"></script>
```

### Trackování kliků
Do html elementu přidate atribut **data-nctr="nazev_ktery_chcete_logovat"**. Pro doplňující informace slouží atribut **data-ncval="hodnota"** a pokud chcete hodnoty slučovat nebo průměrovat, tak k tomu slouží atributy **data-ncavg="hodnota"** a **data-ncsum="hodnota"**
```html
<a href="/test" data-click="testingPage" data-click-value="test" data-click-average="5" data-click-sum="10">Test</a>
```