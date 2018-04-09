{config_load file='default.conf'}{strip}

    <div class="sidebar" data-color="blue">

      <div class="sidebar-wrapper">
          <div class="logo">
              <a href="#" class="simple-text">Una pagina de Prueba</a>
          </div>
          <ul class="nav">
              <li>
                <a href="#">
                    <p>Inicio</p>
                </a>
              </li>
              <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <p>Catalogo</p>
                      <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="adminproductos/index">Productos</a></li>
                      <li><a href="admincategorias/index">Categorias</a></li>
                    </ul>
              </li>
              <li>
                <a href="#">
                  <p>Clientes</p>
                </a>
              </li>
              <li>
                <a href="#">
                    <p>Adminsitracion</p>
                </a>
              </li>
              <li>
                <a href="#">
                    <p>Estadisticas</p>
                </a>
              </li>
            </ul>
      </div>
    </div>


    <div class="main-panel">
      <nav class="navbar navbar-default navbar-fixed">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                <span class="sr-only">Burguer</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Menu Principal</a>
          </div>
       </div>
     </nav>



{/strip}
