-- YOUR WORKING SCRIPT + TEAM CHECK (No Friendly Fire)
-- Settings
local HoldClick = true
local Hotkey = "x"
local HotkeyToggle = true

-- SCOPE DELAY SETTINGS
local ScopeDelay = 0.15
local ScopedIn = false
local ScopeDelayTime = 0

local Players = game:GetService("Players")
local RunService = game:GetService("RunService")
local UserInputService = game:GetService("UserInputService")

local LocalPlayer = Players.LocalPlayer
local Mouse = LocalPlayer:GetMouse()

local Enabled = false
local RightClickHeld = false
local CurrentlyPressed = false

-- TEAM CHECK FUNCTION (NEW!)
local function isEnemy(targetPlayer)
    if not targetPlayer or targetPlayer == LocalPlayer then
        return false
    end
    
    -- Sniper Duels team check
    return targetPlayer.Team ~= LocalPlayer.Team
end

Mouse.KeyDown:Connect(function(key)
    key = key:lower()
    if key == Hotkey:lower() then
        if HotkeyToggle then
            Enabled = not Enabled
            print("Autotrigger:", Enabled and "ON (Team Safe)" or "OFF")
        else
            Enabled = true
        end
    end
end)

Mouse.KeyUp:Connect(function(key)
    key = key:lower()
    if not HotkeyToggle and key == Hotkey:lower() then
        Enabled = false
    end
end)

UserInputService.InputBegan:Connect(function(input, gameProcessed)
    if input.UserInputType == Enum.UserInputType.MouseButton2 then
        RightClickHeld = true
        ScopedIn = true
        ScopeDelayTime = tick()
    end
end)

UserInputService.InputEnded:Connect(function(input, gameProcessed)
    if input.UserInputType == Enum.UserInputType.MouseButton2 then
        RightClickHeld = false
        ScopedIn = false
        if HoldClick and CurrentlyPressed then
            CurrentlyPressed = false
            mouse1release()
        end
    end
end)

RunService.RenderStepped:Connect(function()
    if Enabled and RightClickHeld and ScopedIn then
        local DelayPassed = (tick() - ScopeDelayTime) >= ScopeDelay
        
        if Mouse.Target and Mouse.Target.Parent:FindFirstChild("Humanoid") then
            -- NEW TEAM CHECK!
            local targetPlayer = Players:GetPlayerFromCharacter(Mouse.Target.Parent)
            
            -- Only trigger if it's an ENEMY (not teammate)
            if targetPlayer and isEnemy(targetPlayer) and DelayPassed then
                if HoldClick then
                    if not CurrentlyPressed then
                        CurrentlyPressed = true
                        mouse1press()
                        print("🔥 Enemy hit:", targetPlayer.Name) -- Debug
                    end
                else
                    mouse1click()
                end
            else
                -- Release if targeting teammate or no valid target
                if HoldClick and CurrentlyPressed then
                    CurrentlyPressed = false
                    mouse1release()
                end
            end
        else
            if HoldClick and CurrentlyPressed then
                CurrentlyPressed = false
                mouse1release()
            end
        end
    else
        if HoldClick and CurrentlyPressed then
            CurrentlyPressed = false
            mouse1release()
        end
    end
end)

print("🎯 SNIPER DUELS AutoTrigger + TEAM CHECK Loaded!")
print("Press X to toggle | Won't shoot teammates!"
