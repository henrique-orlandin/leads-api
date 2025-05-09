<h4 align="center" style="color:#7d58c2">Leads API Coding Test</h4>

<p>
    This API application is a coding sample created to evaluate my skills using the following stack:
    <ul>
        <li>PHP</li>
        <li>MySQL</li>
        <li>CodeIgniter 4</li>
        <li>Docker</li>
    </ul>
</p>

<h4 style="color:#7d58c2">Setup Steps</h4>

<p>
    <ol>
        <li>The first step is to locate the files sent by email which will be used on the following steps.</li>
        <li>This project uses laradock container for its docker environment. Run the following command to boot the necessary docker containers: <code>docker-compose up -d nginx mysql phpmyadmin</code></li>
        <li>CodeIgniter 4 has composer dependencies to be installed. Enter the <code>api/</code> folder and run composer install.</li>
        <li>PhpMySql is available for this project. A database dump can be sent by email if necessary. User and password are the default for testing purpose.</li>
        <li>There is a nginx site configuration that can also be sent by email. Add it to the following path <code>/laradock/nginx/sites</code></li>
        <li><strong>.env</strong> files can be sent my email. Please add them to <code>/laradock/.env</code> and <code>/api/.env</code></li>
        <li>Add the API endpoints and environment variables using the Postmand json files.</li>
        <li>If everything is correct, you should be able to see the app home page <a target="_blank" href="http://leads-api.localhost/">here</a>
        <li>If I miss anything on the configuration, please send me a message at henrique.orlandin@gmail.com</li>
    </ol>
</p>

<h4 style="color:#7d58c2">Notes</h4>

<p>
    I had to do a lot of research for this project because I didn't have professional experience with some tools yet. 
</p>
<p>
    CodeIgniter is the PHP framework I am currently working with, but my company works with version 3 which do not have lots of nice features from version 4.
</p>
<p>
    This is the first time I use Laradock, so sorry if I didn't install it correctly.
</p>

<h4 style="color:#7d58c2">Limitations</h4>
<p>
    <ul>
        <li>I tried to use Redis or Memcached for caching, but I couldn't figure out the correct configuration, so I decided to use the default CodeIgniter 4 cache. I guess, I need to learn more about laradock.</li>
        <li>I could not find a way of catching all CodeIgniter errors so the used would never see a weird error message of some server vulnerability. So my application is missing a full error catch.</li>
        <li>I could not find a good load/stress testing tool for my application.</li>
        <li>I didn't want to take too long to send this test, so I tried to make things simple. In a real app, I would create an user authentication to generate the API token and have the user ID on the logs table. I would also write unit tests against the API endpoints.</li>
        <li>Since I feel that there are some things missing on my test, I decided to code an UI page for the API testing. This way I could show some frontend knowledge as well. So this UI will be found in the home page and it is very simple, it uses Bootstrap and jQuery.</li>
    </ul>
</p>

<h4 style="color:#7d58c2">Important</h4>
<p>
    If possible, I would like some feedback from my test. I want to know how the application performed, what I should do differently and anything else I could add to it. Thanks.
</p>
