<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class StockService
{

    /**
     * Nhập thêm hàng vào kho.
     */
    public function import(
        string $stockType,
        int $itemId,
        int $quantity,
        ?int $updatedBy = null,
        ?string $note = null
    ): Model {

        if ($quantity <= 0) {

            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng nhập kho phải lớn hơn 0.',
            ]);

        }


        return DB::transaction(function () use (
            $stockType,
            $itemId,
            $quantity,
            $updatedBy,
            $note
        ) {

            $item = $this->lockStockItem(
                $stockType,
                $itemId
            );


            $before = (int) $item->stock_quantity;

            $after = $before + $quantity;


            $item->stock_quantity = $after;

            $item->save();



            $this->createHistory(

                stockType: $stockType,

                item: $item,

                transactionType: 'IMPORT',

                quantityBefore: $before,

                quantityChange: $quantity,

                quantityAfter: $after,

                updatedBy: $updatedBy,

                note: $note

            );


            return $item;

        });

    }





    /**
     * Xuất hàng khỏi kho.
     */
    public function export(
        string $stockType,
        int $itemId,
        int $quantity,
        ?int $updatedBy = null,
        ?string $note = null
    ): Model {


        if ($quantity <= 0) {

            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng xuất kho phải lớn hơn 0.',
            ]);

        }



        return DB::transaction(function () use (
            $stockType,
            $itemId,
            $quantity,
            $updatedBy,
            $note
        ) {


            $item = $this->lockStockItem(
                $stockType,
                $itemId
            );



            $before = (int) $item->stock_quantity;



            if ($quantity > $before) {

                throw ValidationException::withMessages([
                    'quantity' =>
                        'Số lượng xuất kho không được lớn hơn số lượng tồn hiện tại.',
                ]);

            }



            $after = $before - $quantity;



            $item->stock_quantity = $after;

            $item->save();




            $this->createHistory(

                stockType: $stockType,

                item: $item,

                transactionType: 'EXPORT',

                quantityBefore: $before,

                quantityChange: -$quantity,

                quantityAfter: $after,

                updatedBy: $updatedBy,

                note: $note

            );



            return $item;


        });


    }





    /**
     * Điều chỉnh tồn kho theo số lượng thực tế.
     */
    public function adjust(
        string $stockType,
        int $itemId,
        int $newQuantity,
        ?int $updatedBy = null,
        ?string $note = null
    ): Model {


        if ($newQuantity < 0) {

            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng tồn kho sau điều chỉnh không được nhỏ hơn 0.',
            ]);

        }



        return DB::transaction(function () use (
            $stockType,
            $itemId,
            $newQuantity,
            $updatedBy,
            $note
        ) {


            $item = $this->lockStockItem(
                $stockType,
                $itemId
            );



            $before = (int) $item->stock_quantity;



            if ($newQuantity === $before) {

                throw ValidationException::withMessages([
                    'quantity' =>
                        'Số lượng mới đang bằng với tồn kho hiện tại.',
                ]);

            }



            $change = $newQuantity - $before;



            $item->stock_quantity = $newQuantity;

            $item->save();




            $this->createHistory(

                stockType: $stockType,

                item: $item,

                transactionType: 'ADJUSTMENT',

                quantityBefore: $before,

                quantityChange: $change,

                quantityAfter: $newQuantity,

                updatedBy: $updatedBy,

                note: $note

            );



            return $item;


        });


    }





    /**
     * Khóa Product trước khi cập nhật tồn kho.
     */
    private function lockStockItem(
        string $stockType,
        int $itemId
    ): Model {


        $stockType = strtoupper(
            trim($stockType)
        );



        if ($stockType !== 'PRODUCT') {

            throw ValidationException::withMessages([
                'stock_type' =>
                    'Loại tồn kho không hợp lệ.',
            ]);

        }



        return Product::query()

            ->lockForUpdate()

            ->findOrFail($itemId);


    }





    /**
     * Ghi lịch sử thay đổi tồn kho.
     */
    private function createHistory(
        string $stockType,
        Model $item,
        string $transactionType,
        int $quantityBefore,
        int $quantityChange,
        int $quantityAfter,
        ?int $updatedBy = null,
        ?string $note = null
    ): StockHistory {


        $stockType = strtoupper(
            trim($stockType)
        );



        return StockHistory::create([

            'stock_type' => $stockType,


            'product_id' => $item->id,


            'transaction_type' =>
                $transactionType,


            'quantity_before' =>
                $quantityBefore,


            'quantity_change' =>
                $quantityChange,


            'quantity_after' =>
                $quantityAfter,


            'order_id' => null,


            'updated_by' =>
                $updatedBy,


            'note' =>
                $note,

        ]);


    }

}