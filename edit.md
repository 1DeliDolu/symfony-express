İşte **ücretsiz Tailwind CSS** ile hemen kullanabileceğin modern **tablo** örnekleri. Hepsi saf HTML/Tailwind; ikinci ve üçüncü örnekte basit arama/sıralama/sayfalama için **Alpine.js** kullanıyorum. Symfony/Twig’te direkt yapıştırıp çalıştırabilirsin.

---

### 1) Basit + Responsive + Zebra + Hover

```html
<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50 text-gray-600">
      <tr>
        <th class="px-4 py-3 text-left font-semibold">Başlık</th>
        <th class="px-4 py-3 text-left font-semibold">Yazar</th>
        <th class="px-4 py-3 text-left font-semibold">Fiyat</th>
        <th class="px-4 py-3 text-left font-semibold">Tarih</th>
        <th class="px-4 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50">
        <td class="px-4 py-3 font-medium text-gray-900">Refactoring</td>
        <td class="px-4 py-3 text-gray-700">Martin Fowler</td>
        <td class="px-4 py-3 text-gray-700">€39.90</td>
        <td class="px-4 py-3 text-gray-700">2023-07-02</td>
        <td class="px-4 py-3 text-right">
          <a class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700">Görüntüle</a>
        </td>
      </tr>
      <!-- satırlar... -->
    </tbody>
  </table>
</div>
```

---

### 2) Arama + Sıralama (Alpine.js ile)

```html
<div x-data="{
  q:'', sort:{key:'title', dir:'asc'},
  rows:[
    {title:'Clean Code', author:'Robert C. Martin', price:29.9, date:'2022-09-01'},
    {title:'DDD', author:'Eric Evans', price:44.0, date:'2021-03-18'},
    {title:'Refactoring', author:'Martin Fowler', price:39.9, date:'2023-07-02'},
  ],
  get filtered(){
    let r = this.rows.filter(x =>
      [x.title,x.author,String(x.price),x.date].join(' ').toLowerCase().includes(this.q.toLowerCase())
    );
    r.sort((a,b)=>{
      const k=this.sort.key, d=this.sort.dir==='asc'?1:-1;
      return (a[k] > b[k] ? 1 : a[k] < b[k] ? -1 : 0) * d;
    });
    return r;
  },
  setSort(k){ this.sort.dir = (this.sort.key===k && this.sort.dir==='asc') ? 'desc':'asc'; this.sort.key=k; }
}">
  <div class="mb-3 flex items-center justify-between gap-3">
    <input x-model="q" type="text" placeholder="Ara..."
           class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"/>
    <span class="text-sm text-gray-500" x-text="filtered.length + ' kayıt'"></span>
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">
            <button @click="setSort('title')" class="font-semibold hover:underline">
              Başlık <span x-show="sort.key==='title'">(<span x-text="sort.dir"></span>)</span>
            </button>
          </th>
          <th class="px-4 py-3 text-left">
            <button @click="setSort('author')" class="font-semibold hover:underline">
              Yazar <span x-show="sort.key==='author'">(<span x-text="sort.dir"></span>)</span>
            </button>
          </th>
          <th class="px-4 py-3 text-left">
            <button @click="setSort('price')" class="font-semibold hover:underline">
              Fiyat <span x-show="sort.key==='price'">(<span x-text="sort.dir"></span>)</span>
            </button>
          </th>
          <th class="px-4 py-3 text-left">
            <button @click="setSort('date')" class="font-semibold hover:underline">
              Tarih <span x-show="sort.key==='date'">(<span x-text="sort.dir"></span>)</span>
            </button>
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <template x-for="row in filtered" :key="row.title">
          <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50">
            <td class="px-4 py-3 font-medium text-gray-900" x-text="row.title"></td>
            <td class="px-4 py-3 text-gray-700" x-text="row.author"></td>
            <td class="px-4 py-3 text-gray-700" x-text="new Intl.NumberFormat('tr-TR',{style:'currency',currency:'EUR'}).format(row.price)"></td>
            <td class="px-4 py-3 text-gray-700" x-text="row.date"></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</div>
```

---

### 3) Sayfalama + Sticky Header (Alpine.js)

```html
<div x-data="{
  page:1, per:5,
  rows: Array.from({length:42}).map((_,i)=>({id:i+1, name:'Kayıt '+(i+1), city:'İstanbul'})),
  get total(){ return Math.ceil(this.rows.length/this.per); },
  get slice(){ const s=(this.page-1)*this.per; return this.rows.slice(s, s+this.per); }
}">
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full text-sm">
      <thead class="sticky top-0 bg-gray-50 text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left font-semibold">ID</th>
          <th class="px-4 py-3 text-left font-semibold">Ad</th>
          <th class="px-4 py-3 text-left font-semibold">Şehir</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <template x-for="r in slice" :key="r.id">
          <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50">
            <td class="px-4 py-3 font-medium text-gray-900" x-text="r.id"></td>
            <td class="px-4 py-3 text-gray-700" x-text="r.name"></td>
            <td class="px-4 py-3 text-gray-700" x-text="r.city"></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <div class="mt-3 flex items-center justify-between">
    <div class="text-sm text-gray-600">
      Sayfa <span class="font-semibold" x-text="page"></span>/<span x-text="total"></span>
    </div>
    <div class="space-x-2">
      <button @click="page=Math.max(1,page-1)" class="rounded-lg border px-3 py-1.5 hover:bg-gray-50">Önceki</button>
      <button @click="page=Math.min(total,page+1)" class="rounded-lg border px-3 py-1.5 hover:bg-gray-50">Sonraki</button>
    </div>
  </div>
</div>
```

---

### 4) Sıkıştırılmış (Compact) Tablo + Badge’ler

```html
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
  <table class="min-w-full text-xs">
    <thead class="bg-gray-50 text-gray-600">
      <tr>
        <th class="px-3 py-2 text-left font-semibold">Mağaza</th>
        <th class="px-3 py-2 text-left font-semibold">Sipariş No</th>
        <th class="px-3 py-2 text-left font-semibold">Adet</th>
        <th class="px-3 py-2 text-left font-semibold">Durum</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <tr class="hover:bg-blue-50/50">
        <td class="px-3 py-2">9952</td>
        <td class="px-3 py-2">ORD-2025-001</td>
        <td class="px-3 py-2">12</td>
        <td class="px-3 py-2">
          <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-green-800">Tamamlandı</span>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

---

### Hızlı Notlar

* **Tailwind** : projende kuruluysa direkt çalışır.
* **Alpine.js** (2 ve 3. örnekler): importmap/AssetMapper ile ekle:

  ```twig
  {{ importmap(['app', 'alpinejs']) }}
  ```

  veya CDN:

  ```html
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  ```
* Twig ile döngü:

  ```twig
  {% for item in items %}
    <tr>...</tr>
  {% endfor %}
  ```

Hazır birini projene uyarlamamı ister misin? Örneğin `titles` ya da `sales` listesini bu tablolardan biriyle şık bir şekilde render edebilirim.
