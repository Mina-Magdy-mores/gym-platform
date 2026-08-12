<x-app-layout>
    <x-slot name="title">Assign Diet Nutrition Plan</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                <i class="ri-restaurant-line text-green-400"></i> Assign Diet Plan for {{ $user->name }}
            </h2>
            <a href="{{ route('trainer.members.index') }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-black uppercase transition">
                <i class="ri-arrow-left-line"></i> Back to Roster
            </a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        title: @js(old('title', $existingDietPlan?->title ?? '')),
        daily_calories: @js(old('daily_calories', $existingDietPlan?->daily_calories ?? 2400)),
        protein_grams: @js(old('protein_grams', $existingDietPlan?->protein_grams ?? 180)),
        carbs_grams: @js(old('carbs_grams', $existingDietPlan?->carbs_grams ?? 220)),
        fats_grams: @js(old('fats_grams', $existingDietPlan?->fats_grams ?? 60)),
        status: @js(old('status', $existingDietPlan?->status ?? 'active')),
        notes: @js(old('notes', $existingDietPlan?->notes ?? '')),
        availableTemplates: @js($availableDietPlans->toArray()),
        selectedTemplateId: '',
        meals: @js(old('meals', $existingDietPlan && $existingDietPlan->meals->count() > 0 ? $existingDietPlan->meals->toArray() : [
            ['meal_name' => 'Breakfast / Pre-Workout', 'meal_time' => '08:00 AM', 'food_items' => '4 Egg Whites + 1 Whole Egg + 100g Oats + 1 Banana', 'calories' => 550],
            ['meal_name' => 'Lunch / Post-Workout', 'meal_time' => '02:00 PM', 'food_items' => '200g Grilled Chicken Breast + 200g White Rice + Green Salad', 'calories' => 750]
        ])),
        loadTemplate(templateId) {
            if (!templateId) return;
            let found = this.availableTemplates.find(t => t.id == templateId);
            if (found) {
                this.title = found.title;
                this.daily_calories = found.daily_calories || 2400;
                this.protein_grams = found.protein_grams || 180;
                this.carbs_grams = found.carbs_grams || 220;
                this.fats_grams = found.fats_grams || 60;
                this.notes = found.notes || '';
                this.status = 'active';
                if (found.meals && found.meals.length > 0) {
                    this.meals = found.meals.map(m => ({
                        meal_name: m.meal_name || 'Meal 1',
                        meal_time: m.meal_time || '08:00 AM',
                        food_items: m.food_items || '',
                        calories: m.calories || 400
                    }));
                }
            }
        },
        addMeal() {
            this.meals.push({ meal_name: 'Meal #' + (this.meals.length + 1), meal_time: '06:00 PM', food_items: '', calories: 400 });
        },
        removeMeal(index) {
            if (this.meals.length > 1) {
                this.meals.splice(index, 1);
            }
        }
    }">
        <form action="{{ route('trainer.diet.store', $user->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Preset Template Selector Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-5 shadow-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-folder-open-line text-green-400"></i> Load Diet Template or Past Nutrition Plan
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Select any previously created diet plan to instantly auto-fill all its macros and meals</p>
                    </div>
                </div>

                <div class="relative">
                    <select x-model="selectedTemplateId" @change="loadTemplate($event.target.value)" class="w-full bg-[#1a1d28] border border-white/15 rounded-xl text-white text-xs p-3 focus:border-green-400">
                        <option value="">-- Choose Past Diet Plan / Master Template to Auto-Fill --</option>
                        @foreach($availableDietPlans as $dietOption)
                            <option value="{{ $dietOption->id }}">
                                🥗 {{ $dietOption->title }} ({{ $dietOption->daily_calories }} Kcal - {{ $dietOption->meals->count() }} Meals) - Status: {{ strtoupper($dietOption->status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Macros Overview Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-pie-chart-2-line text-green-400"></i> Nutrition & Macros Overview
                    </h3>
                    @if($existingDietPlan)
                        <span class="px-2.5 py-1 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-black uppercase">
                            Editing Existing Active Diet Plan #{{ $existingDietPlan->id }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-2 space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Plan Title *</label>
                        <input type="text" name="title" x-model="title" required placeholder="e.g. High Protein 2400 Kcal Cutting Plan" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Daily Calories *</label>
                        <input type="number" name="daily_calories" x-model="daily_calories" required min="500" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Protein (g) *</label>
                        <input type="number" name="protein_grams" x-model="protein_grams" required min="0" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Carbs (g) *</label>
                        <input type="number" name="carbs_grams" x-model="carbs_grams" required min="0" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Fats (g) *</label>
                        <input type="number" name="fats_grams" x-model="fats_grams" required min="0" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-3 space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Coach Nutrition Notes</label>
                        <textarea name="notes" x-model="notes" rows="2" placeholder="e.g. Drink 3.5L water daily. Avoid sugar after 8 PM." class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Status (Visibility)</label>
                        <select name="status" x-model="status" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-green-400 focus:ring-1 focus:ring-green-400">
                            <option value="active">Active (Visible on Member Dashboard)</option>
                            <option value="archived">Archived (Hide from Member Dashboard)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dynamic Meals Builder Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-restaurant-2-line text-green-400"></i> Daily Meals Breakdown
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Build daily meals and food items dynamically</p>
                    </div>
                    <button type="button" @click="addMeal()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg transition flex items-center gap-1.5 cursor-pointer">
                        <i class="ri-add-line text-base"></i> Add Meal
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(meal, index) in meals" :key="index">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-3 relative">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-green-400 uppercase font-mono" x-text="'Meal #' + (index + 1)"></span>
                                <button type="button" @click="removeMeal(index)" x-show="meals.length > 1" class="text-red-400 hover:text-red-300 transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                                    <i class="ri-delete-bin-line"></i> Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-6 gap-3">
                                <div class="lg:col-span-2 space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Meal Title *</label>
                                    <input type="text" :name="'meals[' + index + '][meal_name]'" x-model="meal.meal_name" required placeholder="Breakfast" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Timing</label>
                                    <input type="text" :name="'meals[' + index + '][meal_time]'" x-model="meal.meal_time" placeholder="08:00 AM" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="lg:col-span-2 space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Food Items & Ingredients *</label>
                                    <input type="text" :name="'meals[' + index + '][food_items]'" x-model="meal.food_items" required placeholder="e.g. 4 Eggs + 100g Oats" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Est. Calories</label>
                                    <input type="number" :name="'meals[' + index + '][calories]'" x-model="meal.calories" placeholder="500" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-white/10 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition cursor-pointer">
                        Assign Diet Plan to Athlete
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
