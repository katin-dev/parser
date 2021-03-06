Spike
======

Spike - это лучший некомпилируемый шаблонизатор для PHP

За основу синтаксиса был взят шаблонизатор https://github.com/pyrocms/lex , более ничего общего с Lex наш шаблонизатор не имеет

## Переменные
```
{{ name }}
{{ user.name }}
{{ category.description|escape }} - применение модификатора к переменной
```

## Условные операторы:
```
{{ if active }}	<!-- проверка переменной на true или false -->
	<h1>Welcome</h1>
	<!-- вложенные условные операторы -->
	{{ if user.wallet.amount > min_cost }} <!-- сравнение двух переменных -->
		{{ if user.age > 21 }} <!-- сравнение переменной и числа -->
			Maybe some hot drinks?
		{{else}}
			Would You like some coffe?
		{{/if}}
		{{ if user.name == "Sergey" }}	<!-- сравнение переменной и строки -->
			Hello, Sergey! Here is yor cherry juice!
		{{ /if }}
	{{ else }}
		It looks like you must find Job!
	{{ /if }}
{{ else }}
  We are closed now, Sorry
{{ /if}}
```

В условных операторах можно использовать символы && и ||
```
{{ if user.active && user.age > 21 }}
 Lest's drink, dude!
{{ /if }}
```

## Циклы
```php 
$parser->parse($template, [
 'goods' => [
    ['name' => 'Pineapple'],
    ['name' => 'Melone'],
    ['name' => 'Kiwi'],
 ]
]);
```

Простое использование (содержимое переменной $template): 
```
{{ goods }}
<li>{{name}}</li>
{{ /goods }}
```

Циклы с параметрами <b>item</b> и <b>key</b>
```
{{ user.goods item="good" key="key" }} 
<b>{{key}} : {{good.name}}</b>
{{/user.goods}}
```

Использование вспомогательных переменных <b>_is_first_</b> <b>_is_last_</b> <b>_pos_</b>
```
{{ user.goods item="good" }} 
  <div {{if good._is_first_}}class="first"{{/if}}>
    <span class="number">{{good._pos_}}</span>
    <strong>{{good.name}}</strong>
  </div>
  {{ if good._is_last_ == 0 }}
  <div class="separator"></div>
  {{/if}}
{{/user.goods}}
```


## Callback
Для начала, следует установить callback: 
```php
$parser->setCallback(function ($name, $options, $content) {
    // обработка callback тегов
});
```
После этого в шаблонах можно писать
```
{{ module.news.getList ids = page.ids order="date" }}
Здесь можно передать шаблон в callback (параметр $content) 
{{ /module.news.getList }}
```
Обратите внимание: 
* переменные в параметрах указываются без кавычек `ids = page.ids`
* строковые параметры указываются через `двойные кавычки` : `order = "date desc"`
* в параметрах можно указывать модификаторы:
```
{{ spellcount value = goods|count words="товар,товара,товаров" }}
```

## Передача параметров в callback вызовы

Параметры можно передавать как строки, тогда их надо обрамлять кавычками (двойными или одинарными):
```
{{ getUserAge user="ivan" }} // используем двойные кавычки
<a href="{{ url page='news' route='page'}}">Новости</a> // используем одинарные кавычки, чтоб не ломать HTML подсветку IDE
```

Переменные надо передавать напрямую - без кавычек
```
{{ getUserAge user=userID }} // Передаём значение переменной userID
```

## Модификаторы
Модификаторы - это те же callback. Когда мы пишем {{ goods|count }} после получения значения для переменной goods, вызывается callback, которому во втором аргументе $options 
будет передано значение value = $goods. 

Модификаторм можно передавать другие аргументы через скобки: 
```
{{ goods|count|spellcount("товар", "товара", "товаров", false) }}
```
В качестве аргументов могут выступать переменные, тогда мы указываем их без кавычек:
``` 
{{ var|modificator_name(var_name) }}
```

<b>true</b> и <b>false</b> Интерпретируется как булево значение TRUE и FALSE

## Присвоение значений переменным
Шаблонизатор позволяет создавать новые переменные в шаблоне, присваивая им значение с помощью записи вида <code>{{ set var="varname"}}присваиваемое значение{{/set}}</code>
### Присвоение переменной текстового содержимого
```
{{ set var="myContent"}}
  {{items}}
 	<li>{{name}}</li>
  {{/items}}
{{ /set }}

{{ if myContent }}
  <h1>Список чего-нибудь</h1>
  <ul>{{myContent}}</ul>
{{ /if }}
```

### Присвоение переменной любого другого значения
```
{{ set var="items" raw="1"}}
  {{ module.catalog.getItems }}
{{ /set }}

{{ if items}}	
  <h1>Список чего-нибудь</h1>
  <ul>
	{{items}}
		<li>{{name}}</li>
	{{/items}}
  </ul>
{{ /if }}
```



## Комментарии
```
{{* это комментарий *}}
```


