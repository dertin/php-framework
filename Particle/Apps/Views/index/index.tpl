{config_load file='default.conf'}{strip}
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <div class="container" id="main">
      <div class="row">
        <h3>View made with Vue.js</h3>
        <p class="text-center text-primary"> {{ message }} </p>
        <p class="text-center text-secondary"> {{ message2 }} </p>
      </div>

      <div class="row">
        <h3> Test Vue.js </h3>
        <button class="btn btn-default" v-on:click="getContentList"> List Content</button>
        <ul class="list-group" id="listCont" style="display: none;" ref="listCont">
          <li v-for="item in contentList" class="list-group-item">
            {{ item }}
          </li>
        </ul>
      </div>
    </div>

    <script type="text/javascript">
      var url = 'https://jsonplaceholder.typicode.com/users';
      new Vue ({
        el: '#main',
        data: {
          message: 'This is a complete PHP Framework',
          message2: 'The views were built with Vue.js framework',
          contentList: ['ORM', 'Views', 'Controllers', 'Router', 'Cache', 'SessionHandler', 'PHPUnit'],
          userList: []
        },
        methods: {
          getUserList: function () {
            axios.get(url).then(response =>  {
              this.userList = response.data
            });
          },
          getContentList: function () {
            this.$refs.listCont.style = 'block';
          },
        }
      });
    </script>
{/strip}
