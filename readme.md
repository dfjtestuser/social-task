# social-task

This is a demonstration project for fetching your facebook posts and storing into database.


## install
```bash
  git clone https://github.com/defji/social-task
  cd social-task
  composer intall
  npm install
  php artisan migrate
  php artisan key:generate
  # edit your  .env file for database access
  # set FACEBOOK_APP_ID and FACEBOOK_APP_SECRET
  gulp
```

You can register with "login with Facebook" button, later can both access with your email and 'facebook' for passwword.

## Try online
 * http://myposts.dfj.hu

## TODO
 * make posts prettier
 * refactoring
 * tests
