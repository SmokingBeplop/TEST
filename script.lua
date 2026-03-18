-- Settings
local HoldClick = true
local Hotkey = "x"
local HotkeyToggle = true
local TeamCheck = true -- ignores players on your team
local IgnoreFriendlyNPCs = true -- ignores NPCs with same/friendly team markers

-- BASE DELAY SETTINGS
local MinDelay = 0.05
local MaxDelay = 0.20
local Sensitivity = 0.002

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

-- Flick tracking
local LastMousePos = Vector2.new(0, 0)
local FlickSpeed = 0

local function getCharacterFromTarget(target)
	if not target then
		return nil
	end
	return target:FindFirstAncestorOfClass("Model")
end

local function getNPCFriendlyState(character)
	if not character then
		return false
	end

	-- Common patterns used by games for NPC team/friendly markers
	local friendly = character:FindFirstChild("Friendly")
	local isFriendly = character:FindFirstChild("IsFriendly")
	local teamValue = character:FindFirstChild("Team")
	local teamColorValue = character:FindFirstChild("TeamColor")

	if friendly and friendly:IsA("BoolValue") and friendly.Value == true then
		return true
	end

	if isFriendly and isFriendly:IsA("BoolValue") and isFriendly.Value == true then
		return true
	end

	if teamValue then
		if teamValue:IsA("ObjectValue") and teamValue.Value == LocalPlayer.Team then
			return true
		end
		if teamValue:IsA("StringValue") and LocalPlayer.Team and teamValue.Value == LocalPlayer.Team.Name then
			return true
		end
	end

	if teamColorValue and LocalPlayer.TeamColor then
		if teamColorValue:IsA("BrickColorValue") and teamColorValue.Value == LocalPlayer.TeamColor then
			return true
		end
		if teamColorValue:IsA("StringValue") and teamColorValue.Value == tostring(LocalPlayer.TeamColor) then
			return true
		end
	end

	return false
end

local function isValidEnemyTarget(target)
	local character = getCharacterFromTarget(target)
	if not character then
		return false
	end

	local humanoid = character:FindFirstChildOfClass("Humanoid")
	if not humanoid or humanoid.Health <= 0 then
		return false
	end

	local targetPlayer = Players:GetPlayerFromCharacter(character)
	if targetPlayer then
		if TeamCheck and targetPlayer.Team == LocalPlayer.Team then
			return false
		end
		return true
	end

	-- NPC handling
	if IgnoreFriendlyNPCs and getNPCFriendlyState(character) then
		return false
	end

	return true
end

Mouse.KeyDown:Connect(function(key)
	key = key:lower()

	if key == Hotkey:lower() then
		if HotkeyToggle then
			Enabled = not Enabled
			print("Autotrigger:", Enabled and "ON" or "OFF")
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

RunService.RenderStepped:Connect(function(dt)
	local currentPos = UserInputService:GetMouseLocation()
	local delta = (currentPos - LastMousePos).Magnitude
	FlickSpeed = delta / (dt > 0 and dt or 1)
	LastMousePos = currentPos

	local DynamicDelay = math.clamp(
		MaxDelay - (FlickSpeed * Sensitivity),
		MinDelay,
		MaxDelay
	)

	if Enabled and RightClickHeld and ScopedIn then
		local DelayPassed = (tick() - ScopeDelayTime) >= DynamicDelay

		if DelayPassed and isValidEnemyTarget(Mouse.Target) then
			if HoldClick then
				if not CurrentlyPressed then
					CurrentlyPressed = true
					mouse1press()
				end
			else
				mouse1click()
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
