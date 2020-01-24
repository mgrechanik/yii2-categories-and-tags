# Иерархические категории (или теги) из Active Record моделей под Yii2

[English version](../README.md)

## Содержание

* [Цель](#goal)
* [Демо](#demo)
* [Установка](#installing)
* [Дефолтная AR модель категории данного расширения](#default-ar)
* [Использование своей AR модели](#custom-ar)
* [Настройки модуля](#settings)
* [Пример вывода списка категорий на frontend](#frontend-output)



---

## Цель <span id="goal"></span>

Данное расширение предоставляет модуль со следующим функционалом:

1. Соединяет ```Active Record``` модели одной таблицы в дерево по алгоритму ```Materialized path```, с помощью [данного](https://github.com/mgrechanik/yii2-materialized-path) расширения
2. Вы можете использовать свою ```ActiveRecord``` модель, с нужными вам полями, унаследовав ее от базовой модели данного расширения. [Подробнее](#custom-ar)
3. Данный модуль следует структуре [универсального модуля](https://github.com/mgrechanik/yii2-universal-module-sceleton)
4. По сути вы получите набор ```Active Record``` моделей, организованных в дерево, и ```CRUD``` операциями над ними в **backend** части

    * Модуль не предоставляет функционала **frontend**-a, т.к. мы не знаем что будет размещаться в категориях
    * Также подойдет он для организации хранения **системы тегов** (если они организованы иерархически)	
	* Функционал ```CRUD``` страниц обеспечивает возможность указания/изменения позиции узла в дереве на любую допустимую
	* Дальнейшая работа с таким деревом предполагает использование возможностей [Materialized path](https://github.com/mgrechanik/yii2-materialized-path) расширения **!** [Пример](#frontend-output)
	* Индексная страница просмотра списка категорий предполагает вывод **всего** списка, без пагинаций и фильтров

---

## Демо <span id="demo"></span>

Функционал **backend** части будет выглядеть так (если вывод на английском):
![получившийся функционал списка категорий](https://raw.githubusercontent.com/mgrechanik/yii2-categories-and-tags/master/docs/images/categories.png "Функционал дерева категорий")
	
---
    
## Установка <span id="installing"></span>

#### Установка через composer:

Выполните
```
composer require --prefer-dist mgrechanik/yii2-categories-and-tags
```

или добавьте
```
"mgrechanik/yii2-categories-and-tags" : "~1.0.0"
```
в  `require` секцию вашего `composer.json` файла.

#### Миграции

Если вам не требуются дополнительные поля для ```Active Record``` модели категории ([подробнее](#custom-ar)), то таблицу для [дефолтной](#default-ar) категории
вы можете создать выполнив:

```
php yii migrate --migrationPath=@vendor/mgrechanik/yii2-categories-and-tags/src/console/migrations
```

#### Подключение модуля  <span id="setup"></span>

Как говорилось [выше](#goal), данный модуль следует структуре *универсального модуля* и предоставляет при этом
только страницы **backend**-а, то при его подключении укажите следующий режим (```mode```):
```
    'modules' => [
        'category' => [
            'class' => 'mgrechanik\yii2category\Module',
            'mode' => 'backend',
            // Другие настройки модуля
        ],
        // ...
    ],
```

Все. При переходе по адресу ```/category``` вы будете видеть весь ваш древовидный список категорий.

---

## Дефолтная AR модель категории данного расширения  <span id="default-ar"></span> 

**Обязательными** полями для модели категории являются ```id, path, level, weight ``` (`id` при этом - первичный ключ), 
они нужны для хранения позиции в дереве. Остальные поля - уже те, которые требуются вам.

Если вам достаточно только одного дополнительного текстового поля - ```name``` - то для этого в расширении имеется
модель [Category](https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/src/models/Category.php), которая установлена моделью по умолчанию данного модуля.

Именно работа с ней показана на [демо](#demo) выше.


---

## Использование своей AR модели  <span id="custom-ar"></span>   

Если вам недостаточно одного дополнительного поля имени, предоставленного [дефолтной](#default-ar) моделью,
имеется возможность создать свою модель, с нужными вам полями, и указать ее как модель категории.

Для того чтобы все это сделать, вам нужно проделать следующие шаги:

#### А) Настройка своей AR модели <span id="custom-ar-a"></span>

1) Сгенерируйте класс вашей AR модели, из таблицы, для которой взята за основу [миграция для модели Category](https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/src/console/migrations/m180908_094405_create_category_table.php), главное тут - [обязательные](#default-ar) поля

2) Измените код вашей модели полностью идентично как мы сделали для [Category](https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/src/models/Category.php) модели: 
* указать имя таблицы
* указать наследование от ```BaseCategory```
* указать ваши дополнительные поля в ```rules(), attributeLabels()```

3) Укажите данному модулю использовать этот класс модели, через его св-во ```$categoryModelClass```

4) Если у вашей модели нет имени ```name``` то настройте свойство модуля - [```$indentedNameCreatorCallback```](#indented-name)

#### B) Настройка своей модели формы <span id="custom-ar-b"></span>

AR модель и форма у нас не смешаны, поэтому действия похожие на **A)** должны быть произведены и над моделью формы.

1) Создайте свою модель формы, взяв полностью как пример модель [CategoryForm](https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/src/ui/forms/backend/CategoryForm.php). 
В ней мы добавили одно поле - ```name``` - а вы укажите ваши. Не забудьте про наследование от ```BaseCategoryForm```

2) Укажите данному модулю использовать этот класс модели формы, через его св-во ```$categoryFormModelClass```

#### C) Настройка views <span id="custom-ar-c"></span>

Данный модуль имеет возможность настроить [какие views использовать](#setup-views).

Вот те из них, которые несут дополнительную информацию, скопируйте, измените под вашу модель, и укажите модулю.

---

## Настройки модуля <span id="settings"></span>

[Подключяя](#setup) модуль в приложение мы можем воспользоваться следующими его свойствами:

#### ```$categoryModelClass``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Какой класс AR модели категории использовать

#### ```$categoryFormModelClass``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Какой класс модели формы использовать

#### ```$indentedNameCreatorCallback``` <span id="indented-name">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Callback, который сформирует название категории на странице всего
списка категорий с учетом отступа, чтобы отображалось как дерево 

#### ```$categoryIndexView```, ```$categoryCreateView```, ```$categoryUpdateView```, ```$categoryFormView```, ```$categoryViewView``` <span id="setup-views"></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- указывают соответствующие **views**, которые модуль будет использовать. 
Формат смотрите в [документации](https://www.yiiframework.com/doc/api/2.0/yii-base-view#render()-detail)

#### ```$redirectToIndexAfterCreate``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Редиректить ли на страницу всех категорий после создания нового элемента.  
```True``` по умолчанию. При ```false``` будет редиректить на страницу просмотра категории

#### ```$redirectToIndexAfterUpdate``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Аналогично предыдущему пункту но для задачи редактирования

#### ```$validateCategoryModel``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Валидировать ли модель категории перед сохранением.  
По умолчанию ```false``` когда считается что из формы приходят уже валидные данные, ею проверенные

#### ```$creatingSuccessMessage```, ```$updatingSuccessMessage```, ```$deletingSuccessMessage``` 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Тексты flash сообщений.  
Если их менять, то не забудьте обеспечить их переводы в источнике ```yii2category```


---

## Пример вывода списка категорий на frontend <span id="frontend-output"></span>

Если вам теперь нужно это все дерево категорий вывести в любой шаблон, выполняем:
```php
use mgrechanik\yiimaterializedpath\ServiceInterface;
// Эта наша дефолтная AR модель категории:
use mgrechanik\yii2category\models\Category;
use mgrechanik\yiimaterializedpath\widgets\TreeToListWidget;

// получаем сервис управления деревьями
$service = \Yii::createObject(ServiceInterface::class);
// Получаем элемент относительно которого строим дерево.
// В данном случае это корневой элемент
$root = $service->getRoot(Category::class);
// Строим дерево из потомков корневого узла
$tree = $service->buildDescendantsTree($root);
// Выводим на странице
print TreeToListWidget::widget(['tree' => $tree]);
```
*Получим следующее дерево:*
<ul>
<li>Laptops &amp; PC<ul>
<li>Laptops</li>
<li>PC</li>
</ul></li>
<li>Phones &amp; Accessories<ul>
<li>Smartphones<ul>
<li>Android</li>
<li>iOS</li>
</ul></li>
<li>Batteries</li>
</ul></li>
</ul>

