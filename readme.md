# Laravel Vue Component
A helper package to aid working with Vue Components in a (non-SPA) Laravel environment. This package makes injecting page-specific Vue Components a breeze.

Sometimes we come across situations where the project is not using Vue extensively, but some pages could highly benefit from it being present. This package aims at those scenarios and makes it possible to (optionally) wrap pages with dedicated Vue components directly from your PHP backend.      

### When would you use this package?
When building a multi-page application there is often a need to inject some Vue magic into specific pages. However, more often then not, as the project grows, the individual script tags get out of control and become very hard to manage.

### How does this package work?
The package implements a new Blade directive that you can safely use to wrap your yielded content in your Blade templates. By using this new directive you are able to inject a custom, page-specific Vue component into any of your pages while keeping your scripts modular therefore manageable at the same time.

### Installation
> **NOTE** - For those with **Laravel 5.2 or before**: please use [v1.0.2](https://github.com/riverskies/laravel-vue-component/tree/1.0.2) instead!!! 

Install the package through composer:
```bash
$ composer require riverskies/laravel-vue-component
```

If you're running Laravel 5.4 or earlier, add the service provider to your **config/app.php** file:
```php
Riverskies\Laravel\VueComponent\VueComponentServiceProvider::class,
```

### Usage
Wrap your yield block in your layout file with the new directive:
```blade
<div id="app">
    @vue($component)
        @yield('content')
    @endvue
</div>
```

If you have `$component` variable set on your view...
```php
return view('pages.home', ['component' => 'homepage']);
```

...the `content` will be wrapped within a dynamic Vue component:
```html
<component is="homepage" inline-template v-cloak>
    <!-- yielded content -->
</component>
```

To inject the homepage component dynamically, you will need to have the set component (`homepage`) available on your root Vue instance. For example, your app.js file might look like this:
```javascript
import Homepage from './pages/homepage.js';

const app = new Vue({
    el: '#app',
    components: { Homepage }
});
```

### Examples
#### 1. Simple page injection
An example where you connect a blade template to a Vue page instance. Consider following scenario...

In your controller method: 
```php
return view('pages.contact', ['component' => 'contact']);
```
In your `pages.contact.blade` file (note the wrapping `<div>` and the escaped `@{{ ... }}` syntax):
```blade
@section('content')
    <div class='contact-page'>
        <h1>Contact us</h1>
        
        <ul class="errors" v-if="errors.length > 0">
            <li v-for="error in errors">@{{ error }}</li>
        </ul>
        
        <form @submit.prevent='submitForm'>
            <input v-model="email"/>
            <textarea v-model="message">@{{ message }}</textarea>
            <button type='submit'>Submit</button>
        </form>
    </div>
@endsection
```
In your `pages/contact.js` file:
```javascript
export default {
    data() {
        return {
            email: '',
            message: '',
            errors: []
        }
    },
    methods: {
        submitForm() {
            // your code to verify/send form data
        }
    }
}
```

And your main `app.js` file to be like this:
```javascript


import Contact from './pages/contact.js';

const app = new Vue({
    el: '#app',
    components: { Contact }
});
```

With above, you are successfully injected VueJS control into your contact page. 

#### 2. Sending through data
Based on Example 1, you can send through data from the backend directly as well.
Consider following scenario...

In your controller method:
```php
// in your controller method
return view('welcome', [
    'component' => [
        'is' => 'contact',
        'data' => [
            'email' => 'john@example.com'
        ]
    ]
]);
```

In your `pages/contact.js` file:
```javascript
export default {
    props: ['data'],
    data() {
        return {
            email: '',
            message: '',
            errors: [],
            ssData: null
        }
    },
    created() {
        // you must evaluate passed through data!
        this.ssData = eval(this.data);
        
        this.email = this.ssData.email;
    },
    methods: {
        submitForm() {
            // your code to verify/send form data
        }
    }
}
```
This method can be used to send through data from the backend directly.
