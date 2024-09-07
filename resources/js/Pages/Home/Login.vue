<script></script>

<template>
  <div class="h-100 container-fluid">
      <div class="h-100 row d-flex align-items-stretch">
        <div
          class="col-12 col-md-7 col-lg-6 d-flex align-items-start flex-column px-vw-5"
        >
          <header class="mb-auto py-vh-2 col-12">
            <a class="navbar-brand pe-4 fs-4" href="/">
              <img src="/logo.png" class="bi bi-layers-half" height="54" />
              <span class="ms-1 fw-bolder">CFC</span>
            </a>
          </header>
          <main class="mb-auto col-12">
            <h1>Identifiez-vous</h1>

            <form class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label"
                    >Votre numéro de téléphone</label
                  >
                  <input
                    type="number"
                    class="form-control form-control-lg"
                    id="exampleInputEmail1"
                    aria-describedby="emailHelp"
                  />
                  <div id="emailHelp" class="form-text">
                    We'll never share your phone number with anyone else.
                  </div>
                </div>
                <div class="mb-3">
                  <label for="exampleInputPassword1" class="form-label"
                    >Mot de pass</label
                  >
                  <input
                    type="password"
                    class="form-control form-control-lg"
                    id="exampleInputPassword1"
                  />
                </div>
                <div class="mb-3 form-check py-3">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    id="exampleCheck1"
                  />
                  <label class="form-check-label" for="exampleCheck1"
                    >
                    En continuent, ça veut dire que vous vous soumettez à nos termes et politiques de responsabilité que ce rôle vous est donné.
                    <!-- If you really don´t want any newsletter -->
                    <!-- <strong>check this box</strong>. Then you just agree to -->
                    receive our marketing mails and product stuff. If you check this box
                    <strong>we will not send out our newsletter</strong> to you
                    at all...on mondays.
                  </label>
                </div>
                <button type="submit" class="btn btn-primary btn-xl mb-4">
                  Soumettre
                </button>
                <p class="small text-muted border-top py-2">
                  En continuent, ça veut dire que vous vous soumettez à nos termes et
                  politiques de responsabilité que ce rôle vous est donné.
                </p>
              </div>
            </form>
          </main>
        </div>

        <div
          class="col-12 col-md-5 col-lg-6 bg-cover"
          style="background-image: url('/assets/img/webp/colorful.webp')"
        ></div>
      </div>
    </div>
</template>
