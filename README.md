# Laravel Nova Switch Locale Tool

For [Astrotomic's Laravel Translatable](https://docs.astrotomic.info/laravel-translatable/)

This tool provides a nice header dropdown to quickly switch between locales. Locale is cached per user and sets the main app locale.

It also provides a `Language` field which gives you the status of the translation on a specific resource.

**NOTE** If you use *Spatie's Laravel Translatable* then use [Laravel Nova Locale Anywhere Tool](https://github.com/slovenianGooner/locale-anywhere)

## Installation

```
composer require ibakhsh/switch-locale
```

## Usage

To begin using this, you must first register the tool in `NovaServiceProvider` under tools. The tool utilises [Astrotomic's Laravel Translatable](https://docs.astrotomic.info/laravel-translatable/) package.

```
public function tools()
{
    return [
        new SwitchLocale([
            "locales" => [
                "en" => "English",
                "de" => "German"
            ],
            "useFallback" => false,
            "customDetailToolbar" => false //optional
        ])
    ];
}
```



### Dropdown

The package provides a switch for the languages that you have to insert into Nova's layout file. You can do that by overwriting the `layout.blade.php` file and insert it after the user dropdown.

```
…
<dropdown class="ml-auto h-9 flex items-center dropdown-right">
    @include('nova::partials.user')
</dropdown>

<switch-locale-dropdown class="ml-6"></switch-locale-dropdown>
…
```

![](/screens/dropdown.png)

### Resource status field

You can then define the field in the resource.

```
use ibakhsh\SwitchLocale\Language;

    Language::make(__('Translation'))
```

![](/screens/formField.png)
![](/screens/indexView.png)
![](/screens/detailField.png)

### Delete translation toolbar button

The package provides a CustomDetailToolbar component that you can toggle via configuration. Optionally, you can also only grab the `<delete-toolbar-button>` from the package and paste it into your own custom detail toolbar.

![](/screens/toolbar.png)


## Language Permissions

If you want to allow users access to only certain languages this tool can also display which langauges they have access to.

![](/screens/dropdownAccess.png)

### Setup

In order for this to work the user needs to have a `locale` property that returns an object of locale with boolean values for which the user has access to.

```
// User table migration

$table->string('locale')->default('{}');
```

```
// User Model app/User.php

protected $casts = [
    'email_verified_at' => 'datetime',
    'locale' => 'array'
];

public function allowedLocale() {
    return $this->allowedAllLocale() || $this->locale[app()->getLocale()];
}

public function allowedAllLocale() {
    return $this->isAdmin(); // As an example, admin is allowed all locale
}
```

```
// User Nova Resource app/Nova/User.php

use Laravel\Nova\Fields\BooleanGroup;
use ibakhsh\SwitchLocale\SwitchLocale;
…

    BooleanGroup::make(__('Allowed Locale'), 'locale')->options(SwitchLocale::getLocales()),
```

### Example Usage

```
// PostPolicy

<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(User $user, Post $post)
    {
        return $post->status == 'published' || ($user && $user->id == $post->author_id);
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user)
    {
        return $user && $user->allowedLocale();
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post)
    {
        return $user && $user->allowedLocale();
    }

    /**
     * Determine whether the user can update the post.
     */
    public function delete(User $user, Post $post)
    {
        return $user && $user->allowedLocale();
    }
}


```

**Note** You should probably have some other permissions and rules in place to limit users from giving them selves access to languages etc.

## Credits
Forked from [u12206050](https://github.com/u12206050/switch-locale)
Originally forked from [slovenianGooner](https://github.com/slovenianGooner/locale-anywhere)
