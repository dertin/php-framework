{config_load file='default.conf'}{strip}
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <div class="container" id="main">
    <h1>View made with Vue.js</h1>
    <p> {{ message }} </p>
  </div>

  <script type="text/javascript">
    new Vue ({
      el: '#main',
      data: {
        message = 'Este es un framework php front y back end',
      }
    });
  </script>
{/strip}
