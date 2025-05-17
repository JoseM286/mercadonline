<template>
  <div class="page-container">
    <div class="page-header-with-logo">
      <h1>MercadonLine</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>
    
    <div class="page-content">
      <div>
        <h2 class="section-title">Nuestra Misión</h2>
        <p class="page-intro">
          En <strong>MercadonLine</strong> creemos en el poder de lo local. Nacimos con la misión de conectar 
          a los productores de la provincia de Castellón directamente con los consumidores, eliminando 
          intermediarios y reduciendo la huella de carbono asociada a los largos transportes.
        </p>
      </div>
      
      <div>
        <h2 class="section-title">Compromiso con la Economía Local</h2>
        <p class="page-intro">
          Cada compra que realizas en nuestra plataforma contribuye directamente a fortalecer la economía 
          de nuestra provincia. Trabajamos con más de 50 productores locales que cultivan y elaboran 
          sus productos siguiendo métodos tradicionales y sostenibles.
        </p>
      </div>
      
      <div>
        <h2 class="section-title">Transparencia y Trazabilidad</h2>
        <p class="page-intro">
          En cada producto encontrarás información detallada sobre su origen, el productor y los 
          métodos de cultivo o elaboración. Creemos que conocer la historia detrás de lo que consumes 
          es un derecho fundamental.
        </p>
      </div>

      <div>
        <h2 class="section-title">Frescura Directa a tu Mesa</h2>
        <p class="page-intro">
          Imagina recibir en casa los productos más frescos de la huerta, 
        el mar y las granjas de Castellón, ¡directamente del productor 
          a tu puerta! En MercadonLine, acortamos la distancia para que disfrutes 
          de la calidad y el sabor auténtico sin moverte de casa
        </p>
      </div>

      <div>
        <h2 class="section-title">Calidad y Sostenibilidad en Cada Clic</h2>
        <p class="page-intro">
          No solo te ofrecemos productos deliciosos, sino que también priorizamos la sostenibilidad. 
          Al elegir MercadonLine, apoyas prácticas agrícolas y de producción respetuosas con el medio 
          ambiente, contribuyendo a un futuro más verde para nuestra comunidad.
        </p>
      </div>
      
      <div class="centered-container">
        <div class="section-card-accent">
          <h3 class="section-subtitle">¡Únete a nuestra comunidad!</h3>
          <p class="community-text">
            Descubre el sabor auténtico de Castellón mientras apoyas a quienes trabajan día a día 
            para mantener vivas nuestras tradiciones gastronómicas.
          </p>
          <div class="button-container">
            <router-link to="/" class="community-button">Ver nuestros productos</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.centered-container {
  display: flex;
  justify-content: center;
  width: 100%;
  margin-top: var(--spacing-xl);
}

.community-text {
  text-align: center;
  font-size: 1.2rem;
  margin-bottom: var(--spacing-lg);
}

.button-container {
  display: flex;
  justify-content: center;
}

.community-button {
  display: inline-block;
  padding: var(--spacing-sm) var(--spacing-lg);
  background-color: #2c5e1a;
  color: white;
  border: none;
  border-radius: 20px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-align: center;
  text-decoration: none;
  max-width: 250px;
}

.community-button:hover {
  background-color: #3a7a23;
}

.section-subtitle {
  text-align: center;
}
</style>




