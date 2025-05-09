<template>
  <div class="about-container">
    <div class="about-header">
      <h1>MercadonLine</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="logo" />
    </div>
    
    <div class="about-content">
      <h2>Nuestra Misión</h2>
      <p>
        En <strong>MercadonLine</strong> creemos en el poder de lo local. Nacimos con la misión de conectar 
        a los productores de la provincia de Castellón directamente con los consumidores, eliminando 
        intermediarios y reduciendo la huella de carbono asociada a los largos transportes.
      </p>
      
      <h2>Compromiso con la Economía Local</h2>
      <p>
        Cada compra que realizas en nuestra plataforma contribuye directamente a fortalecer la economía 
        de nuestra provincia. Trabajamos con más de 50 productores locales que cultivan y elaboran 
        sus productos siguiendo métodos tradicionales y sostenibles.
      </p>
      
      <h2>Productos de Proximidad</h2>
      <p>
        Nuestro catálogo incluye frutas y verduras de temporada, productos lácteos artesanales, 
        conservas, aceites, vinos y mucho más. Todos ellos comparten una característica común: 
        son cultivados o elaborados a menos de 100 km de distancia.
      </p>
      
      <h2>Beneficios Medioambientales</h2>
      <p>
        Al reducir las distancias de transporte, disminuimos significativamente las emisiones de CO2. 
        Además, nuestros productores implementan prácticas agrícolas sostenibles que respetan el 
        entorno natural de Castellón y preservan la biodiversidad local.
      </p>
      
      <h2>Transparencia y Trazabilidad</h2>
      <p>
        En cada producto encontrarás información detallada sobre su origen, el productor y los 
        métodos de cultivo o elaboración. Creemos que conocer la historia detrás de lo que consumes 
        es un derecho fundamental.
      </p>
      
      <div class="about-cta">
        <h3>¡Únete a nuestra comunidad!</h3>
        <p>
          Descubre el sabor auténtico de Castellón mientras apoyas a quienes trabajan día a día 
          para mantener vivas nuestras tradiciones gastronómicas.
        </p>
        <router-link to="/productos" class="cta-button">Ver nuestros productos</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.about-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--spacing-lg);
}

.about-header {
  display: flex;
  align-items: center;
  gap: var(--spacing-lg);
  border-bottom: 2px solid #2c5e1a;
}

.about-header h1 {
  font-size: 2.5rem;
  color: #2c5e1a;
  margin: 0;
}

.logo {
  height: 120px;
  width: auto;
  margin-left: auto;
  margin-bottom: 20px;
}

.about-content {
  line-height: 1.6;
}

.about-content h2 {
  color: #2c5e1a;
  margin-top: var(--spacing-xl);
  margin-bottom: var(--spacing-md);
  font-size: 1.8rem;
}

.about-content p {
  margin-bottom: var(--spacing-md);
  font-size: 1.1rem;
}

.about-content strong {
  color: #2c5e1a;
}

.about-cta {
  margin-top: var(--spacing-xl);
  padding: var(--spacing-lg);
  background-color: rgba(44, 94, 26, 0.1);
  border-radius: 8px;
  text-align: center;
}

.about-cta h3 {
  color: #2c5e1a;
  font-size: 1.5rem;
  margin-bottom: var(--spacing-md);
}

.cta-button {
  display: inline-block;
  margin-top: var(--spacing-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  background-color: #2c5e1a;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.cta-button:hover {
  background-color: #3a7a23;
}

@media (max-width: 768px) {
  .about-header {
    flex-direction: column;
    text-align: center;
    gap: var(--spacing-md);
  }
  
  .logo {
    height: 60px;
  }
  
  .about-content h2 {
    font-size: 1.5rem;
  }
  
  .about-content p {
    font-size: 1rem;
  }
}
</style>

