<script setup></script>

<template>
    <div>
        <header class="bg-gray-800 text-white p-4">
            <nav
                class="flex items-center justify-between p-4 max-w-4xl mx-auto"
            >
                <div class="space-x-6">
                    <Link href="/">Home</Link>
                    <Link href="/about">About Page</Link>
                </div>
            </nav>
        </header>
        <main class="p-4">
            <slot></slot>
        </main>
    </div>
</template>
