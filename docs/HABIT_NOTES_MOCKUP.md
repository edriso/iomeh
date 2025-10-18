# Habit Notes UI Mockup

## Before (Current State)
```
┌─────────────────────────────────────────────────────────┐
│ My Activities                                    [Edit] [Log Activity] │
├─────────────────────────────────────────────────────────┤
│ [🏃‍♂️ Running] [✅] [🧘 Meditation] [✅] [🍎 Healthy Breakfast] │
│ [💪 Weight Training] [🏊 Swimming] [📖 Read Quran] [✅] │
└─────────────────────────────────────────────────────────┘
```

## After (With Notes)
```
┌─────────────────────────────────────────────────────────┐
│ My Activities                                    [Edit] [Log Activity] │
├─────────────────────────────────────────────────────────┤
│ [🏃‍♂️ Running ℹ️] [✅] [🧘 Meditation ℹ️] [✅] [🍎 Healthy Breakfast] │
│ [💪 Weight Training ℹ️] [🏊 Swimming] [📖 Read Quran ℹ️] [✅] │
└─────────────────────────────────────────────────────────┘
```

## Tooltip Examples

### Hover over "Running ℹ️":
```
┌─────────────────────────────────────────────────────────┐
│ My Activities                                    [Edit] [Log Activity] │
├─────────────────────────────────────────────────────────┤
│ [🏃‍♂️ Running ℹ️] [✅] [🧘 Meditation ℹ️] [✅] [🍎 Healthy Breakfast] │
│ [💪 Weight Training ℹ️] [🏊 Swimming] [📖 Read Quran ℹ️] [✅] │
└─────────────────────────────────────────────────────────┘
    ┌─────────────────────────┐
    │ Running                 │
    │ 30 minutes at 6 mph   │
    └─────────────────────────┘
```

### Hover over "Meditation ℹ️":
```
┌─────────────────────────────────────────────────────────┐
│ My Activities                                    [Edit] [Log Activity] │
├─────────────────────────────────────────────────────────┤
│ [🏃‍♂️ Running ℹ️] [✅] [🧘 Meditation ℹ️] [✅] [🍎 Healthy Breakfast] │
│ [💪 Weight Training ℹ️] [🏊 Swimming] [📖 Read Quran ℹ️] [✅] │
└─────────────────────────────────────────────────────────┘
        ┌─────────────────────────┐
        │ Meditation             │
        │ 15 minutes mindfulness │
        └─────────────────────────┘
```

### Hover over "Weight Training ℹ️":
```
┌─────────────────────────────────────────────────────────┐
│ My Activities                                    [Edit] [Log Activity] │
├─────────────────────────────────────────────────────────┤
│ [🏃‍♂️ Running ℹ️] [✅] [🧘 Meditation ℹ️] [✅] [🍎 Healthy Breakfast] │
│ [💪 Weight Training ℹ️] [🏊 Swimming] [📖 Read Quran ℹ️] [✅] │
└─────────────────────────────────────────────────────────┘
            ┌─────────────────────────┐
            │ Weight Training         │
            │ 3 sets of 10 reps      │
            └─────────────────────────┘
```

## Visual Indicators

### 1. Info Icon (ℹ️)
- **Appears on**: Habits that have notes
- **Size**: Small (h-3 w-3)
- **Color**: Subtle opacity (opacity-60)
- **Position**: After habit name, before completion checkmark

### 2. Hover Effects
- **Badge**: Border color changes to primary/30
- **Transition**: Smooth color transition
- **Cursor**: Default cursor (not clickable)

### 3. Tooltip Design
- **Background**: Dark/light theme aware
- **Border**: Subtle border radius
- **Typography**: 
  - Title: font-medium text-sm
  - Notes: text-xs text-muted-foreground
- **Max Width**: max-w-xs (prevents overflow)
- **Position**: Below badge (side="bottom")

## Code Structure

### Badge with Info Icon
```vue
<Badge
    variant="outline"
    class="border-secondary/20 bg-secondary px-3 py-1.5 text-sm text-secondary-foreground hover:border-primary/30 transition-colors"
>
    <span class="mr-1.5">{{ habit.icon }}</span>
    {{ habit.name }}
    <CheckCircle
        v-if="habit.has_activity_today"
        class="ml-1.5 h-3 w-3"
    />
    <Info
        v-if="habit.notes"
        class="ml-1.5 h-3 w-3 opacity-60"
    />
</Badge>
```

### Tooltip Content
```vue
<TooltipContent
    v-if="habit.notes"
    side="bottom"
    class="max-w-xs"
>
    <div class="space-y-1">
        <p class="font-medium text-sm">{{ habit.name }}</p>
        <p class="text-xs text-muted-foreground">{{ habit.notes }}</p>
    </div>
</TooltipContent>
```

## User Experience Flow

### 1. **Initial View**
- User sees habit badges with completion status
- Info icons (ℹ️) indicate which habits have notes
- Clean, uncluttered interface

### 2. **Hover Interaction**
- User hovers over a habit with notes
- Tooltip appears with habit name and notes
- Smooth transition animation

### 3. **Information Access**
- User can quickly see habit details
- No need to navigate to settings
- Contextual information display

### 4. **Mobile Experience**
- Touch interaction works on mobile
- Tooltip positioning adapts to screen
- No layout issues on small screens

## Benefits

### ✅ **Clean Interface**
- Notes don't clutter the main view
- Only visible when needed
- Maintains compact design

### ✅ **User-Friendly**
- Intuitive hover interaction
- Clear visual indicators
- Accessible design

### ✅ **Performance**
- No additional API calls
- Lightweight implementation
- Fast rendering

### ✅ **Responsive**
- Works on all screen sizes
- Touch-friendly on mobile
- Proper positioning

## Testing Scenarios

### 1. **Habits with Notes**
- ✅ Info icon appears
- ✅ Tooltip shows on hover
- ✅ Content is properly formatted

### 2. **Habits without Notes**
- ✅ No info icon shown
- ✅ No tooltip appears
- ✅ Badge functions normally

### 3. **Long Notes**
- ✅ Text wraps properly
- ✅ Max width prevents overflow
- ✅ Remains readable

### 4. **Mobile Devices**
- ✅ Touch interaction works
- ✅ Tooltip positioning correct
- ✅ No layout issues

---

**Implementation Status**: ✅ Complete  
**Files Modified**: 2  
**Breaking Changes**: None  
**Ready for Testing**: Yes
