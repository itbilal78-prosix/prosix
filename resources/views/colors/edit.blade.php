@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2>Edit Color</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('colors.update', $color->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Color Picker --}}
        <div class="mb-3">
            <label class="form-label">Select Color</label>
            <div id="color-picker"></div> {{-- Pickr attaches here --}}
            <input type="text" id="hex" name="code" value="{{ old('code', $color->code) }}" class="form-control mt-2" placeholder="#FF0000">
        </div>

        {{-- Color Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">Color Name</label>
            <input 
                type="text" 
                class="form-control" 
                name="name" 
                id="name" 
                value="{{ old('name', $color->name) }}" 
                required
            >
        </div>

        <button type="submit" class="btn btn-dark">Update Color</button>
        <a href="{{ route('colors.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

{{-- Pickr CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>

{{-- JS for Pickr --}}
<script>
document.addEventListener('DOMContentLoaded', function(){

    const pickr = Pickr.create({
        el: '#color-picker',
        theme: 'classic',
        default: '{{ old("code", $color->code) }}',
        swatches: [
            '#F44336','#E91E63','#9C27B0','#673AB7','#3F51B5',
            '#2196F3','#03A9F4','#00BCD4','#009688','#4CAF50',
            '#8BC34A','#CDDC39','#FFEB3B','#FFC107','#FF9800','#FF5722'
        ],
        components: {
            preview: true,
            opacity: false,
            hue: true,
            interaction: {
                hex: true,
                rgba: true,
                hsla: true,
                input: true,
                clear: false,
                save: true
            }
        }
    });

    const hexInput = document.getElementById('hex');

    // When user changes color in picker → update HEX input
    pickr.on('change', (color, instance) => {
        const hexColor = color.toHEXA().toString();
        hexInput.value = hexColor.toUpperCase();
    });

    // When user types HEX manually → update picker
    hexInput.addEventListener('input', function(){
        const val = hexInput.value;
        if(/^#[0-9A-Fa-f]{6}$/.test(val)){
            pickr.setColor(val);
        }
    });

    // Optional: auto-fill color name
    const nameInput = document.getElementById('name');
    const colorNames = {
        "#ff0000": "Red",
        "#00ff00": "Green",
        "#0000ff": "Blue",
        "#ffff00": "Yellow",
        "#ff00ff": "Magenta",
        "#00ffff": "Cyan",
        "#000000": "Black",
        "#ffffff": "White",
        "#808080": "Gray",
        "#ffa500": "Orange",
    };

    hexInput.addEventListener('input', () => {
        const hex = hexInput.value.toLowerCase();
        if(colorNames[hex]) {
            nameInput.value = colorNames[hex];
        }
    });

});
</script>
@endsection
