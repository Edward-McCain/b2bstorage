<template>
  <div class="min-h-screen bg-base-100">
    <!-- Navbar -->
    <div class="navbar bg-base-200 shadow-lg">
      <div class="navbar-start">
        <div class="dropdown">
          <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
          </div>
          <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
            <li><a>Главная</a></li>
            <li><a>О проекте</a></li>
            <li><a>Контакты</a></li>
          </ul>
        </div>
        <a class="btn btn-ghost text-xl">B2B Storage</a>
      </div>
      <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
          <li><a>Главная</a></li>
          <li><a>О проекте</a></li>
          <li><a>Контакты</a></li>
        </ul>
      </div>
      <div class="navbar-end">
        <a class="btn btn-primary">Войти</a>
      </div>
    </div>

    <!-- Hero Section -->
    <div class="hero min-h-screen bg-gradient-to-r from-primary to-secondary">
      <div class="hero-content text-center text-neutral-content">
        <div class="max-w-md">
          <h1 class="mb-5 text-5xl font-bold">B2B Storage</h1>
          <p class="mb-5">Современная платформа для управления B2B данными с использованием Laravel и Vue.js</p>
          <button class="btn btn-primary">Начать</button>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-base-100">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Возможности</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
              <h3 class="card-title">Laravel Backend</h3>
              <p>Мощный бэкенд на Laravel с PostgreSQL и Redis</p>
            </div>
          </div>
          <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
              <h3 class="card-title">Vue.js Frontend</h3>
              <p>Современный фронтенд на Vue.js 3 с DaisyUI</p>
            </div>
          </div>
          <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
              <h3 class="card-title">WebSockets</h3>
              <p>Реальное время с Laravel WebSockets</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-base-200 text-base-content">
      <aside>
        <p>Copyright © 2025 - All rights reserved by B2B Global</p>
      </aside>
    </footer>
  </div>
</template>

<script setup>
// Vue 3 Composition API
</script>

<style scoped>
/* Custom styles if needed */
</style> 