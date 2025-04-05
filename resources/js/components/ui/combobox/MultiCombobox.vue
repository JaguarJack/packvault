<script setup lang="ts">
import { ref, computed, watch, toRaw } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator
} from '@/components/ui/command'
import {
    Popover,
    PopoverContent,
    PopoverTrigger
} from '@/components/ui/popover'
import { cn } from '@/lib/utils'
import { Check, ChevronsUpDown, X } from 'lucide-vue-next'

const props = defineProps({
    options: {
        type: Array,
        required: true,
        // 每个选项应该有 { label: string, value: string } 的结构
    },
    placeholder: {
        type: String,
        default: '请选择选项'
    }
})

const selectedValues = defineModel<Array<number>>([])
// 内部状态
const open = ref(false)

// 切换选项的选中状态
const toggleOption = (optionValue) => {
    const index = selectedValues.value.indexOf(optionValue)
    const newValues = [...selectedValues.value]

    if (index !== -1) {
        newValues.splice(index, 1)
    } else {
        newValues.push(optionValue)
    }

    selectedValues.value = newValues
}

// 移除选项
const removeOption = (optionValue) => {
    selectedValues.value = selectedValues.value.filter(v => v !== optionValue)
}

// 清除所有选中的选项
const clearAll = () => {
    selectedValues.value = []
}

// 获取选项的标签
const getOptionLabel = (value) => {
    const option = props.options.find(opt => opt.value === value)
    return option ? option.label : value
}

// 显示的文本
const displayText = computed(() => {
    if (selectedValues.value.length === 0) {
        return props.placeholder
    }

    if (selectedValues.value.length === 1) {
        return getOptionLabel(selectedValues.value[0])
    }

    return `已选择 ${selectedValues.value.length} 项`
})
</script>

<template>
    <div class="relative">
        <!-- 已选择的标签显示区域 -->
        <div v-if="selectedValues.length > 0" class="flex flex-wrap gap-1 mb-1">
            <Badge
                v-for="value in selectedValues"
                :key="value"
                variant="secondary"
                class="px-2 py-1"
            >
                {{ getOptionLabel(value) }}
                <button
                    class="ml-1 ring-offset-background rounded-full outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    @click.stop="removeOption(value)"
                >
                    <X class="h-3 w-3" />
                </button>
            </Badge>
        </div>

        <!-- 使用 Popover 和 Command 组件实现下拉多选 -->
        <Popover v-model:open="open">
            <PopoverTrigger as-child>
                <Button
                    variant="outline"
                    role="combobox"
                    :aria-expanded="open"
                    class="w-full justify-between"
                >
                    {{ displayText }}
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-full p-0">
                <Command>
                    <CommandInput placeholder="搜索选项..." />
                    <CommandEmpty>未找到匹配选项</CommandEmpty>
                    <CommandList>
                        <CommandGroup>
                            <CommandItem
                                v-for="option in options"
                                :key="option.value"
                                :value="option.value"
                                @select="toggleOption(option.value)"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary"
                                        :class="selectedValues.includes(option.value) ? 'bg-primary text-primary-foreground' : 'opacity-50'"
                                    >
                                        <Check
                                            v-if="selectedValues.includes(option.value)"
                                            class="h-3 w-3"
                                        />
                                    </div>
                                    {{ option.label }}
                                </div>
                            </CommandItem>
                        </CommandGroup>
                        <CommandSeparator v-if="selectedValues.length > 0" />
                        <CommandGroup v-if="selectedValues.length > 0">
                            <CommandItem @select="clearAll" value="clear-all">
                                <div class="text-center w-full text-sm">清除所有选择</div>
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </div>
</template>
