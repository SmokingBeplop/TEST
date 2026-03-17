-- SNIPER DUELS Auto Trigger + Team Check (X Toggle)
-- Optimized for Sniper Duels specifically

local Players = game:GetService("Players")
local LocalPlayer = Players.LocalPlayer
local RunService = game:GetService("RunService")
local ReplicatedStorage = game:GetService("ReplicatedStorage")

-- Sniper Duels Specific Settings
local MAX_DISTANCE = 1000 -- Longer range for snipers
local SNIPER_REMOTES = {
    "Hit",
    "Damage",
    "RemoteEvent",
    "ShootRemote",
    "FireRemote"
}

-- Team Check (Sniper Duels uses Teams)
local function isTeammate(player)
    if not player or not player.Character or not LocalPlayer.Character then return true end
    
    -- Sniper Duels team check
    return player.Team == LocalPlayer.Team or player == LocalPlayer
end

-- Find closest enemy
local function getClosestEnemy()
    local closest, closestDist = nil, MAX_DISTANCE
    
    for _, player in pairs(Players:GetPlayers()) do
        if player ~= LocalPlayer and player.Character and player.Character:FindFirstChild("HumanoidRootPart") then
            if not isTeammate(player) then
                local distance = (LocalPlayer.Character.HumanoidRootPart.Position - player.Character.HumanoidRootPart.Position).Magnitude
                if distance < closestDist then
                    closest = player
                    closestDist = distance
                end
            end
        end
    end
    return closest
end

-- Sniper Duels Hit Function
local function hitTarget(target)
    if not target or not target.Character then return end
    
    local targetHRP = target.Character.HumanoidRootPart
    local localHRP = LocalPlayer.Character.HumanoidRootPart
    
    -- Sniper Duels remote patterns
    local args = {
        targetHRP,
        localHRP,
        targetHRP.Position,
        "Head" -- Aim for headshots
    }
    
    -- Try all common Sniper Duels remotes
    for _, remoteName in pairs(SNIPER_REMOTES) do
        pcall(function()
            local remote = ReplicatedStorage:FindFirstChild(remoteName)
            if remote and remote:IsA("RemoteEvent") then
                remote:FireServer(unpack(args))
            end
        end)
    end
    
    -- Mouse simulation for click detectors
    mouse1click()
    wait(0.01)
    mouse1release()
end

-- Auto Trigger Loop
local autoTriggerConnection
local function startAutoTrigger()
    if autoTriggerConnection then return end
    
    autoTriggerConnection = RunService.Heartbeat:Connect(function()
        local target = getClosestEnemy()
        if target then
            hitTarget(target)
        end
    end)
end

local function stopAutoTrigger()
    if autoTriggerConnection then
        autoTriggerConnection:Disconnect()
        autoTriggerConnection = nil
    end
end

-- X KEY TOGGLE
local toggled = false
game:GetService("UserInputService").InputBegan:Connect(function(input)
    if input.KeyCode == Enum.KeyCode.X then
        toggled = not toggled
        if toggled then
            startAutoTrigger()
            print("🎯 SNIPER DUELS AUTO TRIGGER ON - Press X to toggle")
        else
            stopAutoTrigger()
            print("❌ SNIPER DUELS AUTO TRIGGER OFF")
        end
    end
end)

-- Enemy ESP (Red for enemies only)
local function createESP(player)
    if isTeammate(player) then return end
    
    local gui = Instance.new("BillboardGui", player.Character.Head)
    gui.Size = UDim2.new(0, 100, 0, 50)
    gui.StudsOffset = Vector3.new(0, 3, 0)
    
    local label = Instance.new("TextLabel", gui)
    label.Size = UDim2.new(1, 0, 1, 0)
    label.BackgroundTransparency = 1
    label.Text = player.Name
    label.TextColor3 = Color3.new(1, 0, 0) -- Red for enemies
    label.TextStrokeTransparency = 0
    label.Font = Enum.Font.SourceSansBold
end

-- Add ESP to players
for _, player in pairs(Players:GetPlayers()) do
    if player.Character then createESP(player) end
    player.CharacterAdded:Connect(function() wait(1) createESP(player) end)
end

print("🎮 SNIPER DUELS Script Loaded!")
print("Press X to toggle | Red ESP = Enemies Only")
