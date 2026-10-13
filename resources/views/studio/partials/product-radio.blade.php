@php

    $image =

        $product->images

            ->firstWhere(

                'is_primary',

                true

            )

        ??

        $product->images->first();



    $inputId =

        $prefix . '_' . $product->id;

@endphp




<div class="product-option">


    <input

        type="radio"

        name="{{ $field }}"

        id="{{ $inputId }}"

        value="{{ $product->id }}"

        @checked(

            old($field, $defaults[$field] ?? null) == $product->id

        )

    >




    <label

        class="product-card"

        for="{{ $inputId }}"

    >



        @if ($image)


            <img

                class="product-image"

                src="{{ asset(

                    'storage/' .

                    $image->image_path

                ) }}"

                alt="{{ $product->name }}"

            >



        @else


            <div class="no-image">

                Không ảnh

            </div>


        @endif





        <div class="product-info">



            <div class="product-name">

                {{ $product->name }}

            </div>




            <div class="product-sku">

                {{ $product->sku }}

            </div>




            <div class="product-bottom">



                <span class="price">


                    {{ number_format(

                        (float)(

                            $product->sale_price

                            ??

                            $product->list_price

                        ),

                        0,

                        ',',

                        '.'

                    ) }}đ



                </span>




                <span class="stock">

                    Còn

                    {{ $product->stock_quantity }}

                </span>



            </div>



        </div>



    </label>


</div>