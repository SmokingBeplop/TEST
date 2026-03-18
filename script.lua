-- Settings
local HoldClick = true
local Hotkey = "x"
local HotkeyToggle = true
local TeamCheck = true -- ignore players on your team

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

local LastMousePos = Vector2.new(0, 0)
local FlickSpeed = 0

local function isValidTarget(target)
	if not target or not target.Parent then return false end
	
	-- QUICK CHECK FIRST (Version 1 style)
	local humanoid = target.Parent:FindFirstChildOfClass("Humanoid")
	if not humanoid then 
		-- Try parent chain for NPCs
		local parent = target.Parent
		while parent do
			humanoid = parent:FindFirstChildOfClass("Humanoid")
			if humanoid then break end
			parent = parent.Parent
		end
	end
	
	if not humanoid or humanoid.Health <= 0 then return false end
	
	-- TEAM CHECK - CRITICAL FIX
	if TeamCheck then
		local character = humanoid.Parent
		local targetPlayer = Players:GetPlayerFromCharacter(character)
		if targetPlayer then
			-- BLOCK SELF AND TEAMMATES
			if targetPlayer == LocalPlayer then return false end
			if targetPlayer.Team == LocalPlayer.Team or targetPlayer.TeamColor == LocalPlayer.TeamColor then
				return false
			end
		end
		-- ALLOW NPCs when TeamCheck is true (non-players pass through)
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
		
		-- FIXED TARGET CHECK
		if Mouse.Target and isValidTarget(Mouse.Target) and DelayPassed then
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
