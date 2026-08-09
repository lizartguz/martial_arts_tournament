<div x-data="sliderComponent({{ json_encode($images) }})"
    class="relative w-full mx-auto overflow-hidden"
    @mouseenter="stopAutoSlide()"
    @mouseleave="startAutoSlide()">

    <!-- Contenedor de Slides -->
    <div class="relative flex transition-transform duration-700 ease-in-out" x-ref="carousel">
        @foreach($images as $image)
            <div class="relative flex-shrink-0 w-full">
                <img src="{{ asset($image['src']) }}" class="object-cover w-full">
                @if (isset($image['title']) && isset($image['description']))
                    <div class="absolute bottom-0 left-0 w-full p-4 text-white bg-black bg-opacity-50">
                        <h2 class="text-2xl font-semibold uppercase">{{ $image['title'] }}</h2>
                        <p>{{ $image['description'] }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Botón Anterior -->
    <button @click="prevSlide()"
            class="absolute hidden w-10 h-10 p-2 text-white -translate-y-1/2 bg-gray-800 rounded-full shadow-md left-2 top-1/2 hover:bg-gray-700 md:block">
        ❮
    </button>

    <!-- Botón Siguiente -->
    <button @click="nextSlide()"
            class="absolute hidden w-10 h-10 p-2 text-white -translate-y-1/2 bg-gray-800 rounded-full shadow-md right-2 top-1/2 hover:bg-gray-700 md:block">
        ❯
    </button>

    <!-- Indicadores -->
    <div class="absolute flex space-x-2 transform -translate-x-1/2 bottom-2 left-1/2">
        @foreach($images as $key => $image)
            <button @click="goToSlide({{ $key }})"
                :class="index === {{ $key }} ? 'bg-white' : 'bg-gray-500'"
                class="w-3 h-3 transition-colors duration-300 rounded-full"
            >
            </button>
        @endforeach
    </div>
</div>

<script>
    function sliderComponent(images) {
        return {
            index: 0,
            slides: images.length,
            autoSlideInterval: null,

            init() {
                this.startAutoSlide();
            },

            startAutoSlide() {
                this.autoSlideInterval = setInterval(() => this.nextSlide(), 3000);
            },

            stopAutoSlide() {
                clearInterval(this.autoSlideInterval);
            },

            updateSlide() {
                this.$refs.carousel.style.transform = `translateX(-${this.index * 100}%)`;
            },

            prevSlide() {
                this.index = (this.index === 0) ? this.slides - 1 : this.index - 1;
                this.updateSlide();
            },

            nextSlide() {
                this.index = (this.index + 1) % this.slides;
                this.updateSlide();
            },

            goToSlide(slideIndex) {
                this.index = slideIndex;
                this.updateSlide();
            }
        }
    }
</script>
