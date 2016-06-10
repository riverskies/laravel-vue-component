# Laravel Vue Component

A helper package to aid working with Vue Components in a (non-SPA) Laravel environment. This package makes injecting page-specific Vue Components a breeze.

### When would you use this package?
When building a multi-page application there is often a need to inject some Vue magic into specific pages. However, more often then not, as the project grows, the individual script tags get out of control and become very hard to manage.

### How does this package work?
The package implements a new Blade directive that you can safely use to yield content in your Blade templates. By using this new directive you are able to inject a custom, page-specific Vue Component into any of your pages while keeping your Vue files organised.

### Installation

Install the package through composer:

```sh
$ composer require riverskies/laravel-vue-component
```

Add the service provider to your **config/app.php** file:

```php
Riverskies\Laravel\VueComponent\VueComponentServiceProvider::class
```

Use the new Blade directive in your layout file:

```php
@vue($vueComponent)
    @yield('content')
@endvue
```

If you have `$vueComponent` variable set on your view, the `content` will be wrapped within a Vue Component. If `$vueComponent` is not set, the `content` will show as usual.

Consider following scenario:
```php
// in your controller method
return view('welcome', ['vueComponent'=>'homepage']);
```

Your template will look like:
```php
<component is="homepage" inline-template v-cloak>
    @yield('content')
</component>
```

You can also pass data to your Vue Component:
```php
// in your controller method
$vueComponent = [
    'is'    => 'homepage',
    'data'  => [ name: 'John', age: 23 ]
];

return view('welcome', compact('vueComponent'));
```

An expample for your `resources/assets/js/app.js` file (utilising browserify with elixir):
```js
import Vue from 'vue';
import Homepage from './pages/homepage.js';

Vue.component('homepage', Homepage);

new Vue({ el: 'body' });
```

An example for your `resources/assets/js/pages/homepage.js` file:
```js
export default {
    props: ['data'],
    created: function() {
        this.data = eval(this.data);
    },
    ready: function() {
        console.log(this.data.something)
    }
}
```
